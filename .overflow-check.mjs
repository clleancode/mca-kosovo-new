import {spawn} from 'node:child_process';
import {mkdtemp} from 'node:fs/promises';
import os from 'node:os';
import path from 'node:path';
const profile=await mkdtemp(path.join(os.tmpdir(),'mca-overflow-'));
const chrome=spawn('C:/Program Files/Google/Chrome/Application/chrome.exe',['--headless=new','--disable-gpu','--no-first-run','--remote-debugging-port=9227',`--user-data-dir=${profile}`,'about:blank'],{windowsHide:true,stdio:'ignore'});
try {
 let tabs;
 for(let i=0;i<40;i++){try{tabs=await(await fetch('http://127.0.0.1:9227/json')).json();break;}catch{await new Promise(r=>setTimeout(r,250));}}
 const ws=new WebSocket(tabs.find(t=>t.type==='page').webSocketDebuggerUrl);
 await new Promise(r=>ws.addEventListener('open',r,{once:true}));
 let id=0;const pending=new Map();
 ws.addEventListener('message',e=>{const v=JSON.parse(e.data);if(v.id){const p=pending.get(v.id);pending.delete(v.id);v.error?p.reject(v.error):p.resolve(v.result);}});
 const send=(method,params={})=>new Promise((resolve,reject)=>{const n=++id;pending.set(n,{resolve,reject});ws.send(JSON.stringify({id:n,method,params}));});
 await send('Emulation.setDeviceMetricsOverride',{width:390,height:844,deviceScaleFactor:1,mobile:true});
 await send('Page.navigate',{url:'http://mca-new.local/project/acfg/'});
 await new Promise(r=>setTimeout(r,3500));
 for(const width of [390,320,430]){
 await send('Emulation.setDeviceMetricsOverride',{width,height:844,deviceScaleFactor:1,mobile:true});
 await new Promise(r=>setTimeout(r,400));
 const result=await send('Runtime.evaluate',{expression:`(async()=>{await document.fonts.ready;const w=document.documentElement.clientWidth;return {viewport:w,scroll:document.documentElement.scrollWidth,overflow:[...document.querySelectorAll('body *')].map(el=>({el,r:el.getBoundingClientRect()})).filter(({el,r})=>r.right>w+1&&r.width>0&&getComputedStyle(el).position!=='fixed').map(({el,r})=>({tag:el.tagName,classes:el.className,left:Math.round(r.left),right:Math.round(r.right),width:Math.round(r.width),section:el.closest('section')?.className})).slice(0,35)};})()`,awaitPromise:true,returnByValue:true});
 console.log(JSON.stringify(result.result.value));
 }
 ws.close();
} finally {chrome.kill();}
