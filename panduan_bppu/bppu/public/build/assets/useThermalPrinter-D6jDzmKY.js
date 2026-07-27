function v(){const l=(e,t="",n=32)=>{const a=String(e),i=String(t),o=n-a.length-i.length;return a+" ".repeat(Math.max(0,o))+i},p=(e,t=32)=>{const n=String(e),a=Math.floor((t-n.length)/2);return" ".repeat(Math.max(0,a))+n},u=(e="-",t=32)=>e.repeat(t),m=e=>new Intl.NumberFormat("id-ID").format(e),E=e=>{const t=new Date(e),n={year:"numeric",month:"short",day:"2-digit",hour:"2-digit",minute:"2-digit",timeZone:"Asia/Jakarta"};return t.toLocaleDateString("id-ID",n)},S=e=>{let t=[];t.push({text:p("BPPU"),bold:!0}),t.push({text:p("IKIP SILIWANGI"),bold:!0});const n=e.work_unit_name||e.workUnit?.nama||"Kantin";t.push({text:p(n)}),t.push({text:p("Jl. Terusan Jendral Sudirman")}),t.push({text:p("Cimahi, Jawa Barat")}),t.push({text:u("=")}),t.push({text:`No: ${e.nomor_transaksi||"-"}`}),t.push({text:`Tgl: ${E(e.tanggal||new Date)}`}),t.push({text:u("-")}),(e.items||[]).forEach(r=>{const b=r.nama||r.nama_barang||"Item",f=r.qty||0,d=r.subtotal||f*(r.harga_satuan||r.harga||0);t.push({text:b});const P=`${f}x`,w=`Rp ${m(d)}`;t.push({text:l(`  ${P}`,w)})}),t.push({text:""}),t.push({text:u("-")});const i=e.subtotal||e.total||0;if(t.push({text:l("Subtotal:",`Rp ${m(i)}`)}),e.diskon&&e.diskon>0&&t.push({text:l("Diskon:",`Rp ${m(e.diskon)}`)}),e.potongan_poin&&e.potongan_poin>0&&t.push({text:l("Potongan Poin:",`Rp ${m(e.potongan_poin)}`)}),t.push({text:u("=")}),t.push({text:l("TOTAL:",`Rp ${m(e.total)}`)}),t.push({text:u("=")}),e.metode_pembayaran){const r=e.metode_pembayaran.toUpperCase();t.push({text:`Pembayaran: ${r}`})}e.bayar&&e.bayar>0&&t.push({text:l("Bayar:",`Rp ${m(e.bayar)}`)}),e.kembalian&&e.kembalian>0&&t.push({text:l("Kembalian:",`Rp ${m(e.kembalian)}`)}),e.poin_didapat&&e.poin_didapat>0&&(t.push({text:""}),t.push({text:p("* POIN REWARD *")}),t.push({text:p(`+${e.poin_didapat} poin`)})),t.push({text:""}),t.push({text:u("-")}),t.push({text:p("Terima Kasih")}),t.push({text:u("-")});const o=e.member_name||e.buyer?.name,h=e.member_id||e.buyer?.member_code;return o&&(t.push({text:p(o)}),t.push({text:p(`Member ID: ${h||"-"}`)})),t.push({text:""}),t.push({text:""}),t.push({text:""}),t},T=async e=>{if(!navigator.bluetooth)throw new Error("Web Bluetooth API tidak didukung di browser ini.");try{const t=await navigator.bluetooth.requestDevice({acceptAllDevices:!0,optionalServices:["000018f0-0000-1000-8000-00805f9b34fb","e7810a71-73ae-499d-8c15-faa9aef0c3f2","49535343-fe7d-4ae5-8fa9-9fafd205e455"]}),n=await t.gatt.connect();let a=null;const i=await n.getPrimaryServices();for(const x of i)try{const s=await x.getCharacteristics();for(const c of s)if(c.properties.write||c.properties.writeWithoutResponse){a=c;break}if(a)break}catch{continue}if(!a)throw new Error("Tidak dapat menemukan karakteristik write pada printer");const o=new TextEncoder,h="\x1B",r="",b=h+"@",f=h+"a1",d=h+"a0",P=h+"E1",w=h+"E0",_=h+"!",$=h+"!\0",D=r+"V\0",N=h+"d";let I="";e.forEach(x=>{const s=typeof x=="string"?{text:x}:x,c=s.text||"";let g="";s.bold&&(g+=P),s.small&&(g+=_),c.includes("===")?g+=f:c.includes("---")||c.trim()&&!s.bold&&!s.small?g+=d:(s.bold||s.small)&&s.bold&&(c.includes("BPPU")||c.includes("IKIP"))?g+=f:g+=d,I+=g+c+`
`,s.bold&&(I=I.trimEnd()+w+`
`),s.small&&(I=I.trimEnd()+$+`
`)});let L=b+I+N+D;const k=o.encode(L),R=20;for(let x=0;x<k.length;x+=R){const s=k.slice(x,Math.min(x+R,k.length));await a.writeValue(s),await new Promise(c=>setTimeout(c,50))}return setTimeout(()=>{t.gatt.disconnect()},1e3),!0}catch(t){throw console.error("Bluetooth print error:",t),t}},y=e=>{const t=window.open("","_blank","width=302,height=600");let n="";e.forEach(a=>{const i=typeof a=="string"?{text:a}:a,o=i.text||"";i.bold?n+=`<strong>${o}</strong>
`:i.small?n+=`<small>${o}</small>
`:n+=`${o}
`}),t.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="utf-8">
        <title>Struk BPPU</title>
        <style>
          @page {
            size: 58mm auto;
            margin: 0;
          }
          body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 5mm;
            width: 58mm;
            background: white;
          }
          pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
          }
          strong {
            font-weight: bold;
          }
          small {
            font-size: 9px;
          }
        </style>
      </head>
      <body>
        <pre>${n}</pre>
      </body>
      </html>
    `),t.document.close(),t.onload=()=>{t.focus(),t.print(),setTimeout(()=>t.close(),1e3)}},A=({dateRange:e,items:t,total:n,totalVerified:a,totalTransaksi:i})=>{let o=[];return o.push({text:p("BPPU IKIP SILIWANGI"),bold:!0}),o.push({text:u("=")}),o.push({text:p("RINGKASAN ITEM")}),o.push({text:p(e)}),o.push({text:u("=")}),[...t].sort((r,b)=>{const f=(r.nama_barang||"Item").toLowerCase(),d=(b.nama_barang||"Item").toLowerCase();return f.localeCompare(d)}).forEach((r,b)=>{const f=r.nama_barang||"Item",d=r.total_qty||0,P=r.total_harga||0,w=r.supplier_name||"-";o.push({text:`${b+1}. ${f}`});const _=`${d}x Rp${m(P)}`,$=w.length>12?w.substring(0,10)+"..":w;o.push({text:l(`  ${_}`,`M:${$}`)})}),o.push({text:u("-")}),o.push({text:l("TOTAL:",`${t.length} item`)}),o.push({text:u("=")}),o.push({text:l("TOTAL:",`Rp ${m(n)}`)}),i&&i>0&&o.push({text:l("Verified:",`${a||0}/${i}`)}),o.push({text:u("=")}),o.push({text:""}),o};return{printReceipt:async e=>{if(e.isItemSummary){const t=A(e);if(navigator.bluetooth&&/Android|iPhone|iPad|iPod/i.test(navigator.userAgent))try{await T(t)}catch(n){console.warn("Bluetooth print failed, falling back to window.print:",n),y(t)}else y(t)}else{const t=S(e);if(navigator.bluetooth&&/Android|iPhone|iPad|iPod/i.test(navigator.userAgent))try{await T(t)}catch(n){console.warn("Bluetooth print failed, falling back to window.print:",n),y(t)}else y(t)}},generateReceiptContent:S,generateItemSummaryContent:A,formatCurrency:m,formatDate:E}}export{v as u};
