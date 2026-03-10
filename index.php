<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AlfaSec XSS Lab 1.0</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
background:#000;
color:#00ff88;
font-family:monospace;
overflow-x:hidden;
}

/* Splash */
#splash{
position:fixed;
top:0;left:0;
width:100%;
height:100%;
background:#000;
display:flex;
align-items:center;
justify-content:center;
font-size:48px;
color:#00ff88;
z-index:9999;
animation:fadeOut 3s forwards;
}
@keyframes fadeOut{
0%{opacity:1}
80%{opacity:1}
100%{opacity:0;visibility:hidden}
}

/* Code Rain */
canvas{
position:fixed;
top:0;
left:0;
z-index:-1;
}

/* Header */
.header{
text-align:center;
padding:30px;
font-size:28px;
letter-spacing:2px;
text-shadow:0 0 20px #00ff88;
}

.container{
max-width:1200px;
margin:auto;
padding:20px;
}

.grid{
display:grid;
grid-template-columns:repeat(auto-fill,minmax(120px,1fr));
gap:15px;
}

.task{
background:#111;
border:1px solid #00ff88;
padding:15px;
text-align:center;
cursor:pointer;
transition:0.3s;
}
.task:hover{
background:#003322;
box-shadow:0 0 15px #00ff88;
}
.task.solved{
background:#002b18;
}

/* Modal */
.modal{
display:none;
position:fixed;
top:0;left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.9);
}
.modal-content{
background:#111;
max-width:600px;
margin:60px auto;
padding:30px;
border:2px solid #00ff88;
}
input{
width:100%;
padding:10px;
margin:10px 0;
background:#000;
border:1px solid #00ff88;
color:#00ff88;
}
button{
background:#000;
border:1px solid #00ff88;
color:#00ff88;
padding:10px 20px;
cursor:pointer;
}
.success{color:#00ff88;margin-top:10px}
</style>
</head>
<body>

<div id="splash">⚡ AlfaSec ⚡</div>
<canvas id="rain"></canvas>

<div class="header">AlfaSec XSS Training Lab v1.0</div>

<div class="container">
<h3>Easy</h3>
<div class="grid" id="easy"></div>

<h3 style="margin-top:40px">Medium</h3>
<div class="grid" id="medium"></div>

<h3 style="margin-top:40px">Hard</h3>
<div class="grid" id="hard"></div>
</div>

<div class="modal" id="modal">
<div class="modal-content" id="modalContent"></div>
</div>

<script>
/* Code Rain */
const canvas=document.getElementById("rain");
const ctx=canvas.getContext("2d");
canvas.height=window.innerHeight;
canvas.width=window.innerWidth;
const letters="ALFASECXSS010101";
const fontSize=14;
const columns=canvas.width/fontSize;
const drops=[];
for(let x=0;x<columns;x++)drops[x]=1;
function draw(){
ctx.fillStyle="rgba(0,0,0,0.05)";
ctx.fillRect(0,0,canvas.width,canvas.height);
ctx.fillStyle="#00ff88";
ctx.font=fontSize+"px monospace";
for(let i=0;i<drops.length;i++){
const text=letters[Math.floor(Math.random()*letters.length)];
ctx.fillText(text,i*fontSize,drops[i]*fontSize);
if(drops[i]*fontSize>canvas.height&&Math.random()>0.975)
drops[i]=0;
drops[i]++;
}
}
setInterval(draw,33);

/* Task Engine */
let progress=JSON.parse(localStorage.getItem("xssProgress"))||{};

function createTasks(container,start,count,level){
for(let i=0;i<count;i++){
let num=start+i;
let div=document.createElement("div");
div.className="task";
if(progress[num])div.classList.add("solved");
div.innerHTML="Task "+num;
div.onclick=()=>openTask(num,level);
container.appendChild(div);
}
}

createTasks(document.getElementById("easy"),1,10,"easy");
createTasks(document.getElementById("medium"),11,10,"medium");
createTasks(document.getElementById("hard"),21,10,"hard");

function openTask(num,level){
document.getElementById("modal").style.display="block";
document.getElementById("modalContent").innerHTML=`
<h2>XSS Task ${num} (${level})</h2>
<p>Maqsad: Alert chiqarish</p>
<form id="form">
<input id="payload" placeholder="Payload kiriting">
<button>Test</button>
</form>
<div id="output"></div>
<div id="result"></div>
<button onclick="closeModal()">Close</button>
`;

document.getElementById("form").onsubmit=function(e){
e.preventDefault();
let p=document.getElementById("payload").value;

if(level==="easy"){
document.getElementById("output").innerHTML=p;
}
if(level==="medium"){
document.getElementById("output").innerHTML=p.replace(/script/gi,"");
}
if(level==="hard"){
document.getElementById("output").innerHTML=p.replace(/script|onerror|onload/gi,"");
}

if(p.toLowerCase().includes("alert")){
document.getElementById("result").innerHTML="<div class='success'>Solved ✅</div>";
progress[num]=true;
localStorage.setItem("xssProgress",JSON.stringify(progress));
}
}
}

function closeModal(){
document.getElementById("modal").style.display="none";
}
</script>

</body>
</html>
