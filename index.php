<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Enrollment - Final Unlimited</title>
<style>
*{box-sizing:border-box;font-family:'Segoe UI',Arial}body{background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;padding:20px;margin:0}.container{max-width:1300px;margin:auto;background:#fff;padding:30px;border-radius:20px}.search-wrap input{width:100%;padding:14px;border:2px solid #667eea;border-radius:10px;margin:20px 0 10px}button{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:14px;border:none;border-radius:10px;width:100%;font-weight:600;cursor:pointer}input{width:100%;padding:12px;border:2px solid #e0e6ed;border-radius:10px;margin-bottom:12px;background:#f8f9fa}.img-preview{display:flex;gap:15px;justify-content:center;flex-wrap:wrap;margin:20px 0}.img-preview img{width:120px;height:120px;object-fit:cover;border-radius:10px;border:3px dashed #ccc;background:#eee}.img-preview img.has-img{border:3px solid #667eea}table{width:100%;min-width:950px;border-collapse:collapse;background:white}th,td{padding:12px;text-align:center;border-bottom:1px solid #e0e6ed}th{background:#667eea;color:#fff}td img{width:60px;height:60px;object-fit:cover;border-radius:8px;cursor:pointer}.table-wrapper{overflow-x:auto}.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:999;justify-content:center;align-items:center}.modal.active{display:flex}.modal img{max-width:90%;max-height:85vh;border-radius:12px}
</style>
</head>
<body>
<div class="container">
<h1>Student Enrollment System</h1>
<p id="storageInfo" style="text-align:center;color:#667eea;font-weight:600">Loading unlimited storage...</p>
<form id="enrollForm">
<input type="text" id="studentName" placeholder="Full Name" required>
<input type="text" id="course" placeholder="Course" required>
<input type="tel" id="cellphoneNo" placeholder="Cellphone No." required>
<label>Profile Image</label><input type="file" id="profileImg" accept="image/*" required>
<label>Grade 12 Card</label><input type="file" id="cardImg" accept="image/*" required>
<label>Good Moral</label><input type="file" id="goodMoralImg" accept="image/*" required>
<div class="img-preview"><img id="profilePreview"><img id="cardPreview"><img id="goodMoralPreview"></div>
<button type="submit">Enroll Student</button>
</form>
<div class="search-wrap"><input type="text" id="searchBox" placeholder="🔍 Search name, course, phone..."></div>
<p id="studentCount" style="font-weight:600">Total Students: 0</p>
<div class="table-wrapper"><table><thead><tr><th>Profile</th><th>Grade 12</th><th>Good Moral</th><th>Name</th><th>Course</th><th>Phone</th><th>Action</th></tr></thead><tbody id="studentTableBody"></tbody></table></div>
</div>
<div class="modal" id="imgModal" onclick="this.classList.remove('active')"><img id="modalImg"></div>
<script>
let db;const openDB=()=>new Promise(r=>{const q=indexedDB.open("FinalEnrollDB",1);q.onupgradeneeded=e=>e.target.result.createObjectStore("students",{keyPath:"id"});q.onsuccess=e=>{db=e.target.result;r()}});const getAll=()=>new Promise(r=>{const t=db.transaction("students","readonly").objectStore("students").getAll();t.onsuccess=()=>r(t.result)});const saveS=s=>new Promise(r=>{const t=db.transaction("students","readwrite");t.objectStore("students").put(s);t.oncomplete=()=>r()});const delDB=id=>new Promise(r=>{const t=db.transaction("students","readwrite");t.objectStore("students").delete(id);t.oncomplete=()=>r()});
let students=[],p64="",c64="",g64="";
const compress=file=>new Promise(res=>{const rd=new FileReader();rd.onload=e=>{const im=new Image();im.onload=()=>{let w=im.width,h=im.height,m=600;if(w>h){if(w>m){h*=m/w;w=m}}else{if(h>m){w*=m/h;h=m}}const ca=document.createElement("canvas");ca.width=w;ca.height=h;ca.getContext("2d").drawImage(im,0,0,w,h);res(ca.toDataURL("image/jpeg",0.6))};im.src=e.target.result};rd.readAsDataURL(file)});
const enrollForm=document.getElementById("enrollForm"),nameEl=document.getElementById("studentName"),courseEl=document.getElementById("course"),phoneEl=document.getElementById("cellphoneNo"),pIn=document.getElementById("profileImg"),cIn=document.getElementById("cardImg"),gIn=document.getElementById("goodMoralImg"),pPre=document.getElementById("profilePreview"),cPre=document.getElementById("cardPreview"),gPre=document.getElementById("goodMoralPreview"),tbody=document.getElementById("studentTableBody"),countEl=document.getElementById("studentCount"),modal=document.getElementById("imgModal"),modalImg=document.getElementById("modalImg"),searchBox=document.getElementById("searchBox"),info=document.getElementById("storageInfo");
async function setPrev(input,preview,set){input.addEventListener("change",async()=>{const f=input.files[0];if(!f)return;const d=await compress(f);preview.src=d;preview.classList.add("has-img");set(d)})}
setPrev(pIn,pPre,v=>p64=v);setPrev(cIn,cPre,v=>c64=v);setPrev(gIn,gPre,v=>g64=v);
enrollForm.addEventListener("submit",async e=>{e.preventDefault();if(!p64||!c64||!g64)return alert("Upload all 3 images");const s={id:Date.now().toString(),name:nameEl.value.trim(),course:courseEl.value.trim(),cellphone:phoneEl.value.trim(),profile:p64,card:c64,goodMoral:g64};await saveS(s);enrollForm.reset();p64="";c64="";g64="";[pPre,cPre,gPre].forEach(p=>{p.src="";p.classList.remove("has-img")});await load()});
async function load(){students=await getAll();const q=(searchBox.value||"").toLowerCase();const f=students.filter(s=>(s.name+s.course+s.cellphone).toLowerCase().includes(q));tbody.innerHTML="";if(f.length===0)tbody.innerHTML=`<tr><td colspan=7 style="padding:20px;color:#888">${students.length===0?"No students yet - enroll many!":"No result"}</td></tr>`;else f.forEach(s=>{const tr=document.createElement("tr");tr.innerHTML=`<td><img src="${s.profile}" onclick="openM('${s.profile}')"></td><td><img src="${s.card}" onclick="openM('${s.card}')"></td><td><img src="${s.goodMoral}" onclick="openM('${s.goodMoral}')"></td><td>${s.name}</td><td>${s.course}</td><td>${s.cellphone}</td><td><button onclick="delS('${s.id}')" style="width:auto;background:#f5576c;padding:8px 12px;border:none;color:#fff;border-radius:6px">Delete</button></td>`;tbody.appendChild(tr)});countEl.textContent="Total Students: "+f.length;info.textContent="✅ Unlimited Ready - Can add 100+ students | Current: "+students.length}
window.delS=async id=>{if(!confirm("Delete?"))return;await delDB(id);await load()};window.openM=s=>{modalImg.src=s;modal.classList.add("active")};searchBox.addEventListener("input",load);(async()=>{await openDB();await load()})();
</script>
</body>
</html>
