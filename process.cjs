const fs = require('fs');

// ---------- helpers ----------
function hav(a, b) {
  const R = 6371000;
  const dLat = (b[1]-a[1])*Math.PI/180, dLon = (b[0]-a[0])*Math.PI/180;
  const la1 = a[1]*Math.PI/180, la2 = b[1]*Math.PI/180;
  const h = Math.sin(dLat/2)**2 + Math.cos(la1)*Math.cos(la2)*Math.sin(dLon/2)**2;
  return 2*R*Math.asin(Math.sqrt(Math.max(0,Math.min(1,h))));
}
const lineLen = pts => { let s=0; for(let i=1;i<pts.length;i++) s+=hav(pts[i-1],pts[i]); return s; };
const p2 = c => [c[0], c[1]];

// ---------- load network (2D) ----------
const ways = [];
for (const f of ['jalan.geojson','jalan_kabupaten.geojson']) {
  const d = JSON.parse(fs.readFileSync('public/'+f,'utf8'));
  for (const ft of d.features) {
    if (ft.geometry && ft.geometry.type==='LineString' && Array.isArray(ft.geometry.coordinates))
      ways.push(ft.geometry.coordinates.map(p2));
  }
}
console.log('network ways:', ways.length);

// ---------- build graph with snap tolerance ----------
const TOL = 15; // meters
const CELL = TOL/111320;
function buildGraph() {
  const grid = new Map();
  const centers = [];
  const cellOf = (lon,lat)=>Math.floor(lon/CELL)+','+Math.floor(lat/CELL);
  function findOrCreate(lon,lat){
    const ck=cellOf(lon,lat); const [cx,cy]=ck.split(',').map(Number);
    let best=-1,bestD=TOL;
    for(let dx=-1;dx<=1;dx++) for(let dy=-1;dy<=1;dy++){
      const c=(cx+dx)+','+(cy+dy); const arr=grid.get(c); if(!arr) continue;
      for(const id of arr){ const dd=hav([lon,lat],centers[id]); if(dd<bestD){bestD=dd;best=id;} }
    }
    if(best>=0) return best;
    const id=centers.length; centers.push([lon,lat]);
    if(!grid.has(ck)) grid.set(ck,[]); grid.get(ck).push(id);
    return id;
  }
  const adj = new Map();
  const mkEdge=(a,b,g)=>{
    if(a===b) return;
    if(!adj.has(a)) adj.set(a,[]); if(!adj.has(b)) adj.set(b,[]);
    const w=hav(g[0],g[1]);
    adj.get(a).push({to:b, geom:g, w});
    adj.get(b).push({to:a, geom:[g[1],g[0]], w});
  };
  for(const w of ways){
    if(w.length<2) continue;
    let prev=findOrCreate(w[0][0],w[0][1]);
    for(let i=1;i<w.length;i++){
      const cur=findOrCreate(w[i][0],w[i][1]);
      mkEdge(prev,cur,[w[i-1],w[i]]);
      prev=cur;
    }
  }
  return {adj, centers};
}
const t0=Date.now();
const {adj, centers} = buildGraph();
console.log('graph built:', centers.length, 'nodes,', /*edges*/ [...adj.values()].reduce((s,a)=>s+a.length,0)/2, 'edges in', Date.now()-t0, 'ms');
const N = centers.length;

// union-find
const parent = new Array(N).fill(0).map((_,i)=>i);
const find = x=>{ let r=x; while(parent[r]!==r) r=parent[r]; while(parent[x]!==r){const n=parent[x];parent[x]=r;x=n;} return r; };
const union = (a,b)=>{ const ra=find(a),rb=find(b); if(ra!==rb) parent[rb]=ra; };
for(const [a,nbs] of adj) for(const e of nbs) union(a,e.to);

// nearest node
const NCELL = 0.001;
const ngrid = new Map();
for(let i=0;i<N;i++){ const ck=Math.floor(centers[i][0]/NCELL)+','+Math.floor(centers[i][1]/NCELL); if(!ngrid.has(ck)) ngrid.set(ck,[]); ngrid.get(ck).push(i); }
function nearest(lon,lat){
  const ci=Math.floor(lon/NCELL), cj=Math.floor(lat/NCELL);
  for(let r=0;r<800;r++){
    let best=-1,bestD=Infinity;
    for(let dx=-r;dx<=r;dx++) for(let dy=-r;dy<=r;dy++){
      if(Math.max(Math.abs(dx),Math.abs(dy))!==r) continue;
      const arr=ngrid.get((ci+dx)+','+(cj+dy)); if(!arr) continue;
      for(const id of arr){ const dd=hav([lon,lat],centers[id]); if(dd<bestD){bestD=dd;best=id;} }
    }
    if(best>=0) return [best,bestD];
  }
  return [-1,Infinity];
}

// dijkstra
function dijkstra(s){
  const dist = new Map(); const prev = new Map();
  dist.set(s,0);
  const heap=[[0,s]];
  const push=(d,n)=>{ heap.push([d,n]); let i=heap.length-1; while(i>0){const p=(i-1)>>1; if(heap[p][0]<=heap[i][0])break; [heap[p],heap[i]]=[heap[i],heap[p]]; i=p;} };
  const pop=()=>{ const top=heap[0]; const last=heap.pop(); if(heap.length){heap[0]=last; let i=0; while(true){const l=2*i+1,r=2*i+2; let m=i; if(l<heap.length&&heap[l][0]<heap[m][0])m=l; if(r<heap.length&&heap[r][0]<heap[m][0])m=r; if(m===i)break; [heap[m],heap[i]]=[heap[i],heap[m]]; i=m;} } return top; };
  while(heap.length){
    const [d,u]=pop();
    if(dist.get(u)!==d) continue;
    const nbs=adj.get(u); if(!nbs) continue;
    for(const e of nbs){ const nd=d+e.w; if(nd < (dist.has(e.to)?dist.get(e.to):Infinity)){ dist.set(e.to,nd); prev.set(e.to,{from:u, edge:e}); push(nd,e.to);} }
  }
  return {dist, prev};
}
function findPath(s,t){
  const {dist, prev} = dijkstra(s);
  if(!dist.has(t)) return null;
  const nodes=[t];
  while(nodes[nodes.length-1]!==s){ nodes.push(prev.get(nodes[nodes.length-1]).from); }
  nodes.reverse();
  if(nodes.length<2) return null;
  const coords=[];
  for(let i=0;i<nodes.length-1;i++){
    const e=prev.get(nodes[i+1]).edge;
    coords.push(e.geom[0]);
    if(i===nodes.length-2) coords.push(e.geom[1]);
  }
  return coords;
}

// ---------- geocode dictionaries ----------
const ruas = JSON.parse(fs.readFileSync('public/ruas_jalan.geojson','utf8'));
const isBad = c => Math.abs(c[0])<1e-6 && Math.abs(c[1])<1e-6;

// junction dict (exact string reuse)
const validRuas = ruas.features.filter(f=>!isBad(f.geometry.coordinates[0]) && Array.isArray(f.geometry.coordinates[0]));
const jd = new Map();
for(const f of validRuas){
  const s=f.geometry.coordinates[0], e=f.geometry.coordinates[1];
  for(const [k,c] of [[f.properties.titik_awal,s],[f.properties.titik_akhir,e]]){
    const key=String(k||'').toUpperCase().trim();
    if(!key) continue;
    if(jd.has(key)) jd.get(key).push(c); else jd.set(key,[c]);
  }
}
const jdAvg = new Map();
for(const [k,arr] of jd){ const n=arr.length; jdAvg.set(k,[arr.reduce((s,c)=>s+c[0],0)/n, arr.reduce((s,c)=>s+c[1],0)/n]); }

function centroid(poly){
  const rings = poly.type==='MultiPolygon' ? poly.coordinates.flat() : poly.coordinates;
  const ring = rings[0];
  if(!ring || ring.length<3) return null;
  let A=0, cx=0, cy=0;
  for(let i=0;i<ring.length-1;i++){
    const x0=ring[i][0],y0=ring[i][1],x1=ring[i+1][0],y1=ring[i+1][1];
    const cross=x0*y1-x1*y0; A+=cross; cx+=(x0+x1)*cross; cy+=(y0+y1)*cross;
  }
  A/=2;
  if(Math.abs(A)<1e-12){ const xx=ring.reduce((s,p)=>s+p[0],0)/ring.length, yy=ring.reduce((s,p)=>s+p[1],0)/ring.length; return [xx,yy]; }
  return [cx/(6*A), cy/(6*A)];
}
function normName(s){ return String(s||'').toUpperCase().replace(/^DESA\s+/,'').replace(/^KEC(\.|\s)?/,'').replace(/^KAB(\.|\s)?/,'').replace(/[^A-Z0-9]+/g,''); }

function buildPolyDict(file, nameFields){
  const data = JSON.parse(fs.readFileSync('public/'+file,'utf8'));
  const dict = new Map();
  for(const f of data.features){
    if(!f.geometry || !f.geometry.coordinates) continue;
    const c = centroid(f.geometry); if(!c) continue;
    for(const nf of nameFields){
      const nm = normName(f.properties[nf]); if(!nm) continue;
      if(dict.has(nm)) dict.get(nm).push(c); else dict.set(nm,[c]);
    }
  }
  return dict;
}
const desaDict = buildPolyDict('peta_desa.geojson', ['Nama_Desa_']);
const kecDict = buildPolyDict('peta_kecamatan.geojson', ['Kecamatan','Name']);
console.log('desa dict:', desaDict.size, 'kec dict:', kecDict.size);

function resolve(str){
  if(!str) return null;
  const key=String(str).toUpperCase().trim();
  if(jdAvg.has(key)) return jdAvg.get(key);
  const nm=normName(str);
  if(desaDict.has(nm)) return desaDict.get(nm)[0];
  if(kecDict.has(nm)) return kecDict.get(nm)[0];
  for(const [k,v] of desaDict){ if(nm && k && (nm.indexOf(k)>=0 || k.indexOf(nm)>=0)) return v[0]; }
  for(const [k,v] of kecDict){ if(nm && k && (nm.indexOf(k)>=0 || k.indexOf(nm)>=0)) return v[0]; }
  return null;
}
function namaTokens(nama){ const t=String(nama||'').split('-').map(x=>x.trim()).filter(Boolean); return t.length===2?t:null; }

// ---------- process ----------
const results = { matched:0, straight:0, dropped:0, unresolved:[], unroutable:[] };
const outFeatures = [];
const unresolvedFeatures = [];

for(const f of ruas.features){
  const p = f.properties;
  let src = isBad(f.geometry.coordinates[0]) ? null : f.geometry.coordinates[0].slice(0,2);
  let dst = isBad(f.geometry.coordinates[1]) ? null : f.geometry.coordinates[1].slice(0,2);
  const srcOrig = !!src, dstOrig = !!dst;

  if(!src || !dst){
    // recover broken endpoints
    let s2 = resolve(p.titik_awal), d2 = resolve(p.titik_akhir);
    if((!s2 || !d2) && namaTokens(p.nama_ruas)){ const t=namaTokens(p.nama_ruas); if(!s2) s2=resolve(t[0]); if(!d2) d2=resolve(t[1]); }
    if(s2 && d2 && Math.abs(s2[0]-d2[0])<1e-6 && Math.abs(s2[1]-d2[1])<1e-6 && namaTokens(p.nama_ruas)){
      const t=namaTokens(p.nama_ruas); const a=resolve(t[0]), b=resolve(t[1]); if(a&&b){ s2=a; d2=b; }
    }
    if(!src) src=s2; if(!dst) dst=d2;
  }

  if(!src || !dst){
    results.dropped++;
    results.unresolved.push({id:p.id, nama:p.nama_ruas, nomor:p.nomor_ruas, awal:p.titik_awal, akhir:p.titik_akhir});
    unresolvedFeatures.push(f);
    continue;
  }
  const [sn,sd]=nearest(src[0],src[1]);
  const [en,ed]=nearest(dst[0],dst[1]);
  if(sn<0||en<0){ results.dropped++; results.unresolved.push({id:p.id, nama:p.nama_ruas, reason:'off-network'}); unresolvedFeatures.push(f); continue; }

  let coords=null;
  if(sn!==en && find(sn)===find(en)) coords = findPath(sn,en);
  if(!coords){
    if(sn===en){ results.dropped++; results.unresolved.push({id:p.id, nama:p.nama_ruas, reason:'degenerate start==end'}); unresolvedFeatures.push(f); continue; }
    // straight fallback: snap recovered/geocoded endpoints to roads; keep original point if it's clearly off-grid
    const SNAP_LIMIT = 150;
    const sStart = srcOrig ? (sd<=SNAP_LIMIT ? centers[sn] : src) : centers[sn];
    const sEnd   = dstOrig ? (ed<=SNAP_LIMIT ? centers[en] : dst) : centers[en];
    coords=[sStart, sEnd];
    results.straight++;
    results.unroutable.push({id:p.id, nama:p.nama_ruas, sd:+sd.toFixed(0), ed:+ed.toFixed(0)});
  } else {
    results.matched++;
  }
  const lengthKm = lineLen(coords)/1000;
  outFeatures.push({
    type:'Feature',
    properties:{ id:p.id, nomor_ruas:p.nomor_ruas, nama_ruas:p.nama_ruas, titik_awal:p.titik_awal, titik_akhir:p.titik_akhir, panjang_km:+lengthKm.toFixed(3), lebar_m:p.lebar_m },
    geometry:{ type:'LineString', coordinates:coords.map(c=>[+c[0].toFixed(6), +c[1].toFixed(6)]) }
  });
}

// ---------- report & write ----------
console.log('\n===== RESULT =====');
console.log('total input features:', ruas.features.length);
console.log('output features:', outFeatures.length);
console.log('matched (traced on network):', results.matched);
console.log('straight fallback (no path):', results.straight);
console.log('dropped (unrecoverable):', results.dropped);
console.log('unrecoverable:', JSON.stringify(results.unresolved, null, 2));
console.log('unroutable (kept straight):', JSON.stringify(results.unroutable, null, 2));
const oldLen = ruas.features.reduce((s,f)=>s+(+f.properties.panjang_km||0),0);
const newLen = outFeatures.reduce((s,f)=>s+f.properties.panjang_km,0);
console.log('old total panjang_km:', oldLen.toFixed(2), '-> new total:', newLen.toFixed(2));

// validity self-check
let badOut = 0;
for(const f of outFeatures){ const c=f.geometry.coordinates; if(!Array.isArray(c)||c.length<2||c.some(p=>!isFinite(p[0])||!isFinite(p[1]))) badOut++; }
console.log('output invalid features:', badOut);

const MODE = process.argv[2] || 'dry';
const out = { type:'FeatureCollection', features: outFeatures };
const unOut = { type:'FeatureCollection', features: unresolvedFeatures };
if(MODE==='write'){
  fs.copyFileSync('public/ruas_jalan.geojson','public/ruas_jalan.geojson.bak');
  fs.writeFileSync('public/ruas_jalan.geojson', JSON.stringify(out));
  if(unresolvedFeatures.length) fs.writeFileSync('public/ruas_jalan_unresolved.geojson', JSON.stringify(unOut));
  console.log('WROTE public/ruas_jalan.geojson (original backed up at .bak) + ruas_jalan_unresolved.geojson');
} else {
  fs.writeFileSync('public/ruas_jalan_cleaned.geojson', JSON.stringify(out));
  if(unresolvedFeatures.length) fs.writeFileSync('public/ruas_jalan_unresolved.geojson', JSON.stringify(unOut));
  console.log('DRY RUN -> wrote public/ruas_jalan_cleaned.geojson (+ _unresolved)');
}