:root{--red:#ed3f23;--purple:#ed3f23;--navy:#003982;--teal:#088486;--forest:#205b41;--green:#25b755;--orange:#ff912a;--yellow:#ffe627;--ink:#1d1d1b;--muted:#5f5f5f;--line:#d9d9d9;--bg:#f5f5f7;--white:#fff}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.5 'Nunito Sans',Arial,sans-serif}a{color:var(--navy);text-decoration:none}a:hover{text-decoration:underline}.topbar{height:76px;display:flex;align-items:center;justify-content:space-between;background:#fff;border-bottom:1px solid var(--line);padding:10px 28px;position:sticky;top:0;z-index:10}.brand{display:flex;align-items:center;gap:12px}.brand img{max-height:48px;max-width:230px}.brand-mark{width:42px;height:42px;background:var(--red);color:#fff;border-radius:50%;display:grid;place-items:center;font-size:24px}.brand strong,.brand span{display:block}.brand strong{font-weight:900}.brand span,.muted,small{color:var(--muted);font-size:.87rem}.top-actions{display:flex;align-items:center;gap:14px}.inline{display:inline}.link-button{border:0;background:none;color:var(--navy);padding:0;font:inherit;cursor:pointer}.sidebar{position:fixed;width:238px;top:76px;bottom:0;background:var(--purple);padding:20px 14px;overflow:auto}.sidebar a{display:block;padding:11px 14px;color:#fff;font-weight:800;border-radius:7px;margin:2px 0}.sidebar a:hover{background:rgba(255,255,255,.15);text-decoration:none}.sidebar hr{border:0;border-top:1px solid rgba(255,255,255,.35);margin:16px 8px}.content{margin-left:238px;padding:34px;min-height:calc(100vh - 76px)}.public-content{max-width:1020px;margin:0 auto;padding:40px 24px;min-height:calc(100vh - 128px)}.footer{background:#fff;border-top:1px solid var(--line);color:var(--muted);font-size:.85rem;padding:17px 28px}.content+.footer{margin-left:238px}.page-heading{display:flex;gap:20px;justify-content:space-between;align-items:flex-start;margin:0 0 24px}.page-heading h1,.hero h1{font-size:clamp(2rem,4vw,3rem);line-height:1.05;margin:0;font-weight:900}.page-heading p{margin:.4rem 0 0;color:var(--muted)}.hero{padding:64px 14px 42px;text-align:center}.hero h1{font-size:clamp(3rem,9vw,5.5rem);margin:6px 0}.hero p{font-size:1.22rem;max-width:650px;margin:0 auto 24px}.eyebrow{color:var(--red);font-weight:900;text-transform:uppercase;letter-spacing:.06em;font-size:.85rem}.actions,.stack{display:flex;gap:10px;flex-wrap:wrap}.actions{justify-content:center}.button{display:inline-block;border:0;border-radius:7px;padding:11px 16px;line-height:1.1;font-weight:900;cursor:pointer;text-decoration:none;font-family:inherit}.button:hover{text-decoration:none;filter:brightness(.96)}.button.primary{background:var(--red);color:#fff}.button.secondary{background:var(--purple);color:#fff}.button.danger{background:#8d2116;color:#fff}.button.small{padding:7px 10px;font-size:.83rem;margin-right:4px}.card{background:#fff;border:1px solid var(--line);border-radius:10px;padding:22px;box-shadow:0 1px 1px rgba(0,0,0,.02)}.card h2{font-size:1.25rem;margin:0 0 10px;font-weight:900}.card-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}.two-col{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-bottom:20px}.metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin-bottom:20px}.metric{border-radius:10px;padding:19px;color:#fff;display:flex;flex-direction:column;min-height:140px;text-decoration:none}.metric:hover{text-decoration:none;filter:brightness(1.03)}.metric strong{font-size:2.8rem;line-height:1;font-weight:900;margin:9px 0}.metric small{color:inherit;opacity:.92}.purple{background:var(--purple)}.red{background:var(--red)}.orange{background:#ce6700}.navy{background:var(--navy)}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.form-grid.narrow{max-width:560px;margin:auto}.form-grid .full,.form-grid button{grid-column:1/-1}.form-grid h2{margin:0}.form-grid label{font-weight:900;display:block;margin:0 0 6px}.form-grid label span{color:var(--red)}input,select,textarea{width:100%;font:inherit;color:var(--ink);background:#fff;border:1px solid #999;border-radius:6px;padding:10px 11px}textarea{resize:vertical}input:focus,select:focus,textarea:focus{outline:3px solid rgba(237,63,35,.28);border-color:var(--purple)}.checkbox{display:flex;align-items:flex-start;gap:10px}.checkbox input{width:auto;margin-top:5px}.checkbox label{font-weight:700}.alert{border-radius:8px;padding:14px 16px;margin:0 0 19px;border-left:6px solid}.alert.success{background:#eaf8ed;border-color:var(--green)}.alert.error{background:#fcebea;border-color:var(--red)}.alert.warning{background:#fff1df;border-color:var(--orange)}.alert.info{background:#fcebea;border-color:var(--purple)}.badge{display:inline-block;border-radius:999px;padding:3px 8px;font-weight:900;font-size:.75rem;line-height:1.15;background:#eee;color:#333;white-space:nowrap}.badge-new,.badge-high,.badge-urgent,.badge-emergency,.badge-unsafe-do-not-use,.badge-damaged{background:#f9d4cf;color:#7b1e13}.badge-in-progress,.badge-assigned,.badge-waiting-for-parts,.badge-waiting-for-contractor,.badge-waiting-for-approval,.badge-needs-attention{background:#ffebd2;color:#7c4200}.badge-resolved,.badge-closed,.badge-approved,.badge-confirmed,.badge-returned,.badge-excellent,.badge-good,.badge-available{background:#d9f3df;color:#125d2e}.badge-normal,.badge-low,.badge-requested,.badge-awaiting-review,.badge-fair,.badge-reserved,.badge-checked-out{background:#fcebea;color:#8d2116}.badge-cancelled,.badge-declined,.badge-lost,.badge-disposed,.badge-out-of-service{background:#e7e7e7;color:#555}.badge-internal{background:#dbeafb;color:#003982}.table-wrap{overflow:auto;border:1px solid var(--line);border-radius:8px}table{width:100%;border-collapse:collapse;min-width:700px;background:#fff}th,td{padding:12px 13px;border-bottom:1px solid var(--line);text-align:left;vertical-align:top}th{font-size:.76rem;text-transform:uppercase;letter-spacing:.04em;background:#f5f5f5;color:#444;font-weight:900}td small,td strong{display:block}.detail-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:20px}.detail-grid .full{grid-column:1/-1}.label{display:block;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;font-size:.75rem;font-weight:900;margin-bottom:4px}.timeline{border-left:3px solid var(--purple);margin-left:5px}.timeline article{position:relative;padding:0 0 18px 21px}.timeline article:before{content:'';position:absolute;width:11px;height:11px;border-radius:50%;background:var(--purple);left:-7px;top:5px}.timeline article.internal:before{background:var(--navy)}.timeline time{display:block;color:var(--muted);font-size:.8rem}.timeline p{margin:6px 0}.attachment-list{display:flex;gap:10px;flex-wrap:wrap}.attachment-list a{background:#eee;padding:8px 11px;border-radius:6px;font-weight:700}.area-card,.equipment-card{background:#fff;border:1px solid var(--line);border-radius:10px;padding:18px;text-decoration:none;color:var(--ink);display:block}.area-card:hover,.equipment-card:hover{text-decoration:none;border-color:var(--purple);box-shadow:0 4px 15px rgba(0,0,0,.08)}.area-card h2,.equipment-card h2{font-size:1.15rem;margin:0 0 5px;font-weight:900}.area-card p{margin:0 0 12px;color:var(--muted)}dl{margin:15px 0 0}.area-card dl{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}.area-card dt,.vertical-dl dt{font-size:.75rem;color:var(--muted)}.area-card dd,.vertical-dl dd{margin:2px 0 0;font-weight:900}.vertical-dl>div{border-bottom:1px solid var(--line);padding:9px 0}.vertical-dl>div:last-child{border-bottom:0}.clean-list{list-style:none;padding:0;margin:0}.clean-list li{padding:10px 0;border-bottom:1px solid var(--line)}.clean-list li:last-child{border-bottom:0}.clean-list small{display:block}.equipment-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:18px}.equipment-card{padding:0;overflow:hidden}.equipment-card>div:last-child{padding:15px}.equipment-photo{height:160px;background:#fcebea;display:grid;place-items:center;color:#8d2116;font-weight:900}.equipment-photo img,.detail-photo{width:100%;height:100%;object-fit:cover;display:block}.detail-photo{max-height:330px;border-radius:8px;margin-bottom:14px}.asset-id{font-family:ui-monospace,monospace;font-size:.78rem;color:var(--navy);font-weight:900;margin:0 0 3px}.honeypot{position:absolute!important;left:-10000px!important}.code,code{font-family:ui-monospace,SFMono-Regular,Consolas,monospace;background:#eee;padding:2px 4px;border-radius:3px;font-size:.85em}@media (max-width:1000px){.metrics{grid-template-columns:repeat(2,1fr)}.detail-grid{grid-template-columns:repeat(2,1fr)}}@media (max-width:760px){.topbar{padding:9px 15px;height:auto;min-height:70px}.top-actions .muted{display:none}.brand strong{font-size:.89rem}.brand img{max-width:160px}.sidebar{position:static;width:auto;padding:8px;background:var(--purple)}.sidebar nav{display:flex;overflow:auto;gap:4px}.sidebar a{white-space:nowrap;padding:8px 10px;margin:0;font-size:.86rem}.sidebar hr{display:none}.content{margin:0;padding:22px 15px}.content+.footer{margin:0}.public-content{padding:28px 15px}.page-heading{display:block}.page-heading .button{margin-top:14px}.two-col,.form-grid,.detail-grid{grid-template-columns:1fr}.detail-grid .full{grid-column:auto}.metrics{grid-template-columns:1fr 1fr}.metric{min-height:115px;padding:15px}.metric strong{font-size:2.1rem}.area-card dl{grid-template-columns:1fr}.footer{padding:15px}.hero{padding:40px 0 28px}.top-actions{gap:8px}}@media (max-width:420px){.metrics{grid-template-columns:1fr}.brand span{font-size:.74rem}}

/* v1.2 polish: clear hierarchy, split ticket tabs and Settings quick actions. */
.topbar{box-shadow:0 2px 12px rgba(0,0,0,.045)}
.brand{min-width:0}.brand img{width:176px;height:50px;max-width:42vw;object-fit:contain;object-position:left center}.brand>div{min-width:0}.brand strong{letter-spacing:-.01em}.brand span{font-weight:700}
.sidebar{box-shadow:inset -1px 0 rgba(255,255,255,.14)}.sidebar a{transition:background .15s ease,transform .15s ease}.sidebar a:hover{transform:translateX(2px)}
.card{box-shadow:0 2px 10px rgba(21,18,30,.045)}.card:hover{box-shadow:0 5px 18px rgba(21,18,30,.065)}
.page-heading{padding-bottom:4px}.page-heading h1{letter-spacing:-.035em}
.tabs{display:flex;gap:8px;align-items:center;border-bottom:1px solid var(--line);margin:0 0 18px;overflow:auto}.tabs a{display:flex;align-items:center;gap:8px;padding:12px 14px;color:var(--ink);font-weight:900;border-bottom:3px solid transparent;white-space:nowrap}.tabs a:hover{text-decoration:none;background:#fff1ef}.tabs a.active{color:var(--purple);border-bottom-color:var(--purple)}.tabs span{display:inline-grid;place-items:center;min-width:22px;height:22px;padding:0 6px;background:#fcebea;border-radius:999px;font-size:.78rem;color:#8d2116}.tabs a.active span{background:var(--purple);color:#fff}
.ticket-type-picker{background:#f5f1fd;border:1px solid #f6c5bd;border-radius:8px;padding:14px}.ticket-form [data-ticket-field]{transition:opacity .15s ease}.ticket-form [data-ticket-field][hidden]{display:none!important}
.badge-whole-site-booked{background:#dbeafb;color:#003982}.badge-whole-site-booked:before{content:'●';font-size:.62rem;margin-right:5px}
.settings-layout{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(280px,.75fr);gap:20px}.settings-side{display:grid;gap:16px;align-content:start}.copy-row{display:flex;gap:8px;align-items:center}.copy-row input{min-width:0;font-size:.86rem;background:#f6f6f6}.copy-row .button{white-space:nowrap;padding:10px 12px}.settings-side .card p:last-child{margin-bottom:0}
.footer{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.footer span:before{content:'·';margin-right:8px;color:#aaa}
[data-whole-site-note]{margin:0}
@media(max-width:960px){.settings-layout{grid-template-columns:1fr}.settings-side{grid-template-columns:repeat(2,minmax(0,1fr))}.settings-side .card:first-child{grid-column:1/-1}}
@media(max-width:760px){.brand img{width:144px;height:42px}.tabs{margin-left:-15px;margin-right:-15px;padding:0 15px}.settings-side{grid-template-columns:1fr}.copy-row{align-items:stretch;flex-direction:column}.copy-row .button{width:100%;text-align:center}.footer{display:block}.footer span:before{display:none}.footer span{display:block;margin-top:3px}.sidebar a:hover{transform:none}}

/* v1.3 — Scouts Red interface, fixed header and clearer booking/reporting controls. */
:root{--purple:#ed3f23}
body{padding-top:76px}
.topbar{position:fixed;inset:0 0 auto 0;width:100%;z-index:100}
.button.secondary{background:var(--red)}
input:focus,select:focus,textarea:focus{outline-color:rgba(237,63,35,.28);border-color:var(--red)}
.alert.info{background:#fcebea;border-color:var(--red)}
.badge-normal,.badge-low,.badge-requested,.badge-awaiting-review,.badge-fair,.badge-reserved,.badge-checked-out{background:#fcebea;color:#8d2116}
.equipment-photo{background:#fcebea;color:#8d2116}

.issue-type-picker{border:0;margin:0;padding:0}.issue-type-picker legend{font-weight:900;margin:0 0 8px}.choice-cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.choice-card{position:relative;display:flex!important;align-items:flex-start;gap:11px;border:1px solid var(--line);border-radius:10px;padding:15px;cursor:pointer;margin:0!important;background:#fff;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease}.choice-card:has(input:checked){border-color:var(--red);background:#fff5f3;box-shadow:0 0 0 3px rgba(237,63,35,.12)}.choice-card input{position:absolute;opacity:0;pointer-events:none}.choice-card strong,.choice-card small{display:block}.choice-card strong{font-size:1.05rem}.choice-card small{margin-top:2px}.choice-icon{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;background:#fcebea;color:var(--red);font-size:1.2rem;font-weight:900;flex:0 0 auto}

.ticket-switcher{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin:0 0 22px}.ticket-switcher a{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:12px;padding:15px 16px;background:#fff;color:var(--ink);text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.02);transition:border-color .15s ease,box-shadow .15s ease,transform .15s ease}.ticket-switcher a:hover{border-color:#f4a195;box-shadow:0 5px 16px rgba(0,0,0,.07);transform:translateY(-1px);text-decoration:none}.ticket-switcher a.active{border-color:var(--red);background:#fff5f3;box-shadow:0 0 0 3px rgba(237,63,35,.12)}.ticket-switcher strong,.ticket-switcher small{display:block}.ticket-switcher small{margin-top:2px}.switcher-icon{display:grid;place-items:center;width:38px;height:38px;border-radius:10px;background:#fcebea;color:var(--red);font-size:1.2rem;font-weight:900}.ticket-switcher b{display:grid;place-items:center;min-width:32px;height:32px;padding:0 9px;border-radius:999px;background:#f4f4f4;color:var(--ink);font-size:.9rem}.ticket-switcher a.active b{background:var(--red);color:#fff}.ticket-list-heading{display:flex;gap:16px;align-items:flex-end;justify-content:space-between;margin:0 0 12px}.ticket-list-heading h2{margin:0;font-size:1.22rem;font-weight:900}.ticket-list-heading p{margin:2px 0 0;color:var(--muted);font-size:.9rem}

.booking-builder{display:grid;gap:18px}.section-heading{display:flex;gap:18px;justify-content:space-between;align-items:flex-start;margin:0 0 13px}.section-heading h2{margin:0}.section-heading p{color:var(--muted);margin:3px 0 0}.selection-count{display:inline-flex;align-items:center;min-height:31px;padding:0 10px;border-radius:999px;background:#fcebea;color:#8d2116;font-weight:900;font-size:.85rem;white-space:nowrap}.equipment-picker-list{border:1px solid var(--line);border-radius:9px;overflow:hidden}.equipment-picker-row{display:grid!important;grid-template-columns:auto 1fr auto;gap:13px;align-items:center;padding:13px 14px;margin:0!important;border-bottom:1px solid var(--line);cursor:pointer;background:#fff}.equipment-picker-row:last-child{border-bottom:0}.equipment-picker-row:has(input:checked){background:#fff5f3}.equipment-picker-row>input{width:19px;height:19px;accent-color:var(--red)}.equipment-picker-name strong,.equipment-picker-name small{display:block}.equipment-picker-name small{margin-top:2px}.quantity-control{display:flex;align-items:center;gap:7px}.quantity-control>label{margin:0!important;font-size:.82rem;white-space:nowrap}.quantity-control input{width:76px;padding:8px}.booking-builder-actions{position:sticky;bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:15px;border:1px solid #f0b4ab;border-radius:10px;background:#fff;padding:13px 15px;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:5}
.availability-grid .card small{display:block;margin-top:10px}
.external-metrics{grid-template-columns:repeat(2,minmax(0,1fr));max-width:650px}

@media(max-width:760px){body{padding-top:70px}.topbar{min-height:70px}.choice-cards,.ticket-switcher{grid-template-columns:1fr}.ticket-list-heading{align-items:flex-start;flex-direction:column;gap:3px}.equipment-picker-row{grid-template-columns:auto 1fr}.quantity-control{grid-column:2;justify-content:flex-start}.booking-builder-actions{align-items:stretch;flex-direction:column;bottom:8px}.booking-builder-actions .button{text-align:center}.external-metrics{grid-template-columns:1fr}.sidebar{top:auto}.content{min-height:calc(100vh - 70px)}}

/* v1.4 — Scout-red navigation and a calmer, clearer working interface. */
:root{
  --purple:#7413dc;
  --red:#ed3f23;
  --navy:#003982;
  --ink:#1f2024;
  --muted:#64666d;
  --line:#e4e5e8;
  --bg:#f7f7f9;
  --surface:#ffffff;
  --surface-soft:#fbfbfc;
  --red-soft:#fff1ee;
  --purple-soft:#f4efff;
  --topbar-height:76px;
}

body{
  background:linear-gradient(180deg,#fafafb 0,#f7f7f9 320px);
  padding-top:var(--topbar-height);
}

a{color:var(--navy)}
.topbar{
  height:var(--topbar-height);
  padding:10px 30px;
  border-bottom:1px solid rgba(30,30,34,.09);
  box-shadow:0 3px 18px rgba(18,20,26,.07);
  backdrop-filter:blur(14px);
}
.brand{gap:13px}
.brand strong{font-size:1rem;letter-spacing:-.02em}
.brand span{font-size:.8rem}
.top-actions{gap:16px}
.top-actions .muted{padding:7px 10px;border-radius:999px;background:#f4f4f6;color:#4d4f57;font-weight:700}
.link-button{color:var(--navy);font-weight:900}

.sidebar{
  top:var(--topbar-height);
  width:258px;
  padding:18px 13px 22px;
  background:var(--red);
  box-shadow:inset -1px 0 rgba(0,0,0,.10), 7px 0 28px rgba(99,23,12,.11);
}
.sidebar-nav{display:flex;flex-direction:column;min-height:100%}
.nav-label{
  margin:8px 11px 7px;
  color:rgba(255,255,255,.78);
  font-size:.69rem;
  font-weight:900;
  letter-spacing:.09em;
  line-height:1;
  text-transform:uppercase;
}
.nav-label-spaced{margin-top:24px}
.sidebar .nav-link{
  display:flex;
  align-items:center;
  gap:11px;
  min-height:44px;
  margin:2px 0;
  padding:10px 12px;
  border:1px solid transparent;
  border-radius:11px;
  color:#fff;
  font-size:.94rem;
  font-weight:800;
  line-height:1.15;
  transition:background .18s ease,color .18s ease,box-shadow .18s ease,transform .18s ease;
}
.sidebar .nav-link:hover{
  background:rgba(255,255,255,.14);
  transform:translateX(2px);
  text-decoration:none;
}
.sidebar .nav-link.is-active{
  background:#fff;
  border-color:rgba(255,255,255,.72);
  color:var(--red);
  box-shadow:0 5px 16px rgba(67,15,8,.19);
}
.nav-icon{
  display:grid;
  flex:0 0 auto;
  place-items:center;
  width:23px;
  height:23px;
  border-radius:7px;
  color:inherit;
  font-size:1rem;
  font-weight:900;
}
.sidebar .nav-link:not(.is-active) .nav-icon{background:rgba(255,255,255,.14)}
.sidebar .nav-link.is-active .nav-icon{background:var(--red-soft)}

.content{margin-left:258px;padding:34px 38px 48px;min-height:calc(100vh - var(--topbar-height))}
.content+.footer{margin-left:258px}
.public-content{max-width:1100px;padding:44px 26px 56px}
.footer{background:rgba(255,255,255,.86);border-top-color:#e8e9ec;padding:18px 30px}

.page-heading{align-items:center;margin-bottom:28px}
.page-heading h1{font-size:clamp(2.05rem,3.7vw,3.05rem);letter-spacing:-.045em}
.page-heading p{max-width:760px;font-size:1rem}
.page-heading .button{box-shadow:0 4px 12px rgba(237,63,35,.22)}

.button{border-radius:10px;padding:12px 17px;transition:transform .16s ease,box-shadow .16s ease,filter .16s ease}
.button:hover{transform:translateY(-1px);box-shadow:0 5px 14px rgba(20,20,26,.12)}
.button.primary{background:var(--red);box-shadow:0 3px 10px rgba(237,63,35,.22)}
.button.secondary{background:#fff;color:var(--navy);border:1px solid #cbd7e5;box-shadow:none}
.button.secondary:hover{background:#f3f7fc;border-color:#afc1d9}
.button.danger{background:#8e2418}

.card,.area-card,.equipment-card{
  border-color:#e7e8ec;
  border-radius:15px;
  box-shadow:0 2px 10px rgba(21,22,28,.045);
}
.card{padding:24px}
.card:hover{box-shadow:0 8px 26px rgba(21,22,28,.075)}
.card h2{letter-spacing:-.02em}
.card-grid,.equipment-grid{gap:20px}
.two-col{gap:20px}

.metrics{gap:17px;margin-bottom:24px}
.metric{
  position:relative;
  overflow:hidden;
  min-height:150px;
  padding:20px;
  border-radius:15px;
  box-shadow:0 8px 22px rgba(22,22,28,.12);
  transition:transform .18s ease,box-shadow .18s ease;
}
.metric:after{content:'';position:absolute;right:-30px;bottom:-44px;width:142px;height:142px;border-radius:50%;background:rgba(255,255,255,.12)}
.metric:hover{transform:translateY(-3px);box-shadow:0 12px 28px rgba(22,22,28,.18)}
.metric span,.metric strong,.metric small{position:relative;z-index:1}
.metric.purple{background:linear-gradient(135deg,#7413dc,#54249b)}
.metric.red{background:linear-gradient(135deg,#ed3f23,#c62d19)}
.metric.orange{background:linear-gradient(135deg,#ee830e,#c96100)}
.metric.navy{background:linear-gradient(135deg,#003982,#00265b)}
.metric strong{font-size:3rem}

input,select,textarea{
  border-color:#b8bbc1;
  border-radius:10px;
  padding:11px 12px;
  background:#fff;
  transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;
}
input:hover,select:hover,textarea:hover{border-color:#878b93}
input:focus,select:focus,textarea:focus{
  border-color:var(--purple);
  outline:3px solid rgba(116,19,220,.15);
  box-shadow:0 0 0 1px rgba(116,19,220,.10);
}
.form-grid{gap:18px}
.form-grid label{margin-bottom:7px}
.form-grid small{margin-top:5px}

.ticket-form{padding:26px;max-width:1020px}
.issue-type-picker legend{font-size:1.08rem}
.choice-card{border-radius:13px;padding:17px}
.choice-card:has(input:checked){border-color:var(--red);background:var(--red-soft);box-shadow:0 0 0 3px rgba(237,63,35,.11)}
.choice-card:hover{border-color:#ec8c7d}
.choice-icon{background:var(--red-soft);color:var(--red)}

.ticket-switcher{gap:14px;margin-bottom:24px}
.ticket-switcher a{border-radius:14px;padding:17px 18px}
.ticket-switcher a:hover{border-color:#be95eb;box-shadow:0 7px 20px rgba(39,20,70,.08)}
.ticket-switcher a.active{border-color:var(--purple);background:var(--purple-soft);box-shadow:0 0 0 3px rgba(116,19,220,.11)}
.ticket-switcher a.active b{background:var(--purple)}
.switcher-icon{background:var(--purple-soft);color:var(--purple)}
.ticket-list-heading{margin-bottom:14px}

.table-wrap{border-radius:14px;border-color:#e3e5e8;box-shadow:0 2px 10px rgba(21,22,28,.035)}
th{background:#f7f7f9;color:#565862;padding:13px 14px}
td{padding:14px;border-bottom-color:#eceef0}
tbody tr{transition:background .14s ease}
tbody tr:hover{background:#fbfbfc}
.badge{padding:4px 9px;border-radius:999px}
.badge-normal,.badge-low,.badge-requested,.badge-awaiting-review,.badge-fair,.badge-reserved,.badge-checked-out{background:var(--purple-soft);color:#5a1fa9}

.timeline{border-left-color:var(--purple)}
.timeline article:before{background:var(--purple)}
.area-card:hover,.equipment-card:hover{border-color:#b68ae5;box-shadow:0 9px 24px rgba(35,20,55,.09)}
.equipment-photo{background:var(--red-soft)}
.booking-builder-actions{border-color:#cbb2e8;border-radius:14px;box-shadow:0 10px 26px rgba(23,16,36,.13)}
.selection-count{background:var(--purple-soft);color:#5a1fa9}
.equipment-picker-row:has(input:checked){background:#fbf7ff}
.equipment-picker-row>input{accent-color:var(--purple)}

@media(max-width:960px){
  .sidebar{width:230px}
  .content{margin-left:230px;padding:30px 26px 42px}
  .content+.footer{margin-left:230px}
}
@media(max-width:760px){
  :root{--topbar-height:70px}
  body{padding-top:var(--topbar-height)}
  .topbar{padding:8px 15px}
  .top-actions .muted{display:none}
  .sidebar{position:static;width:auto;padding:9px 12px;background:var(--red);box-shadow:none}
  .sidebar-nav{display:flex;flex-direction:row;align-items:center;overflow-x:auto;gap:5px;padding-bottom:1px}
  .nav-label{display:none}
  .sidebar .nav-link{min-height:38px;margin:0;padding:8px 10px;border-radius:9px;white-space:nowrap;font-size:.84rem}
  .nav-icon{width:19px;height:19px;font-size:.9rem}
  .sidebar .nav-link:hover{transform:none}
  .content{margin:0;padding:24px 15px 34px;min-height:calc(100vh - var(--topbar-height))}
  .content+.footer{margin:0}
  .public-content{padding:30px 15px 42px}
  .page-heading{align-items:flex-start}
  .card{padding:19px}
  .ticket-form{padding:20px}
  .metric{min-height:126px}
  .metric strong{font-size:2.35rem}
}

/* v1.5 — clearer equipment workflow and settings-based hut configuration. */
.equipment-entry{position:relative;display:flex;flex-direction:column;min-width:0}
.equipment-entry .equipment-card{height:100%}
.equipment-update-link{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:8px;padding:10px 13px;border:1px solid #e1c9f8;border-radius:11px;background:#fff;color:var(--purple);font-weight:900;text-decoration:none;box-shadow:0 2px 8px rgba(40,22,72,.05)}
.equipment-update-link:hover{background:var(--purple-soft);border-color:#b887e8;text-decoration:none}
.form-actions{display:flex;gap:10px;justify-content:flex-end;align-items:center}
.settings-area-config{margin-top:24px;scroll-margin-top:96px}
.settings-area-config h3{font-size:1rem;margin:0 0 10px;font-weight:900}
.compact-list{border:1px solid var(--line);border-radius:12px;overflow:hidden;background:#fff}
.compact-list>a{display:flex;flex-direction:column;gap:2px;padding:12px 13px;border-bottom:1px solid var(--line);color:var(--ink);text-decoration:none}
.compact-list>a:last-child{border-bottom:0}.compact-list>a:hover{background:#fbf7ff;text-decoration:none}.compact-list span{color:var(--muted);font-size:.84rem}.compact-list b{float:right;color:var(--purple);font-weight:900}
.badge-booked{background:var(--purple-soft);color:#5a1fa9}.badge-in-repair{background:#ffebd2;color:#7c4200}.badge-disposed-of{background:#e7e7e7;color:#555}
@media(max-width:760px){.form-actions{justify-content:stretch;flex-direction:column-reverse}.form-actions .button{text-align:center;width:100%}.equipment-update-link{margin-top:7px}}

/* v1.6 — booking calendar and a cleaner Manage navigation. */
.booking-view-switcher{
  display:flex;
  align-items:stretch;
  gap:10px;
  margin:0 0 20px;
}
.booking-view-switcher a{
  display:flex;
  align-items:center;
  gap:11px;
  min-width:0;
  padding:13px 16px;
  border:1px solid #e2e4e8;
  border-radius:13px;
  background:#fff;
  color:var(--ink);
  text-decoration:none;
  box-shadow:0 2px 8px rgba(21,22,28,.035);
}
.booking-view-switcher a:hover{border-color:#f2a497;background:#fff8f6;text-decoration:none}
.booking-view-switcher a.active{border-color:var(--red);background:var(--red-soft);box-shadow:0 0 0 3px rgba(237,63,35,.11)}
.booking-view-switcher a>span:first-child{display:grid;place-items:center;width:31px;height:31px;border-radius:9px;background:#f4f5f7;color:var(--navy);font-size:1.05rem;font-weight:900}
.booking-view-switcher a.active>span:first-child{background:var(--red);color:#fff}
.booking-view-switcher strong{display:block;font-size:.93rem;line-height:1.15}
.booking-view-switcher small{display:block;margin-top:2px;color:var(--muted);font-size:.77rem}

.booking-calendar-shell{padding:0;overflow:hidden}
.booking-calendar-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:22px 24px 14px;border-bottom:1px solid #eceef1;background:linear-gradient(180deg,#fff 0,#fbfbfc 100%)}
.booking-calendar-toolbar h2{margin:0;font-size:1.35rem;letter-spacing:-.03em;text-align:center}
.booking-calendar-toolbar .eyebrow{margin:0 0 3px;text-align:center;font-size:.67rem;font-weight:900;letter-spacing:.08em;color:var(--muted);text-transform:uppercase}
.calendar-nav{display:inline-flex;align-items:center;gap:7px;min-width:100px;padding:9px 11px;border:1px solid #e2e4e8;border-radius:10px;background:#fff;color:var(--navy);font-size:.85rem;font-weight:900;text-decoration:none}
.calendar-nav:hover{border-color:#b5c5d9;background:#f5f8fb;text-decoration:none}
.booking-calendar-toolbar .calendar-nav:last-child{justify-content:flex-end}
.booking-calendar-legend{display:flex;flex-wrap:wrap;gap:9px;padding:13px 24px;border-bottom:1px solid #eceef1;background:#fff}
.calendar-key{display:inline-flex;align-items:center;gap:6px;padding:4px 9px;border-radius:999px;font-size:.74rem;font-weight:800}
.calendar-key:before{content:'';width:7px;height:7px;border-radius:50%;background:currentColor}
.calendar-key.confirmed{color:#205b41;background:#e8f5eb}.calendar-key.approved{color:#5a1fa9;background:var(--purple-soft)}.calendar-key.pending{color:#8c4f00;background:#fff0d9}
.booking-calendar{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));background:#e8eaed;border-top:0;gap:1px}
.booking-calendar-weekday{padding:10px 8px;background:#f5f6f8;color:#5c5e65;font-size:.72rem;font-weight:900;letter-spacing:.06em;text-align:center;text-transform:uppercase}
.booking-calendar-day{display:flex;flex-direction:column;min-height:138px;padding:9px 8px;background:#fff}
.booking-calendar-day.outside-month{background:#fafafb;color:#9699a1}
.booking-calendar-day.today{box-shadow:inset 0 0 0 2px var(--red)}
.booking-calendar-day time{display:flex;align-items:center;justify-content:center;width:26px;height:26px;margin-bottom:6px;border-radius:50%;font-size:.79rem;font-weight:900}
.booking-calendar-day.today time{background:var(--red);color:#fff}
.booking-calendar-events{display:flex;flex-direction:column;gap:4px;min-width:0}
.calendar-event{display:flex;align-items:baseline;gap:4px;min-width:0;padding:4px 5px;border-radius:6px;background:#f1f2f4;color:#3f4148;font-size:.69rem;font-weight:800;line-height:1.2}
.calendar-event span:last-child{display:block;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.calendar-event-time{flex:0 0 auto;font-size:.64rem;font-variant-numeric:tabular-nums;opacity:.76}
.calendar-event.status-confirmed{background:#e8f5eb;color:#205b41}.calendar-event.status-approved{background:var(--purple-soft);color:#5a1fa9}.calendar-event.status-requested,.calendar-event.status-awaiting-approval{background:#fff0d9;color:#8c4f00}

@media(max-width:920px){
  .booking-calendar-day{min-height:116px}
  .calendar-event{font-size:.64rem}
  .calendar-nav{min-width:auto}
}
@media(max-width:680px){
  .booking-view-switcher{display:grid;grid-template-columns:1fr 1fr;gap:8px}
  .booking-view-switcher a{padding:11px 10px;gap:8px}
  .booking-view-switcher small{display:none}
  .booking-calendar-toolbar{padding:16px 12px 12px;gap:8px}
  .calendar-nav{padding:8px;font-size:.78rem}
  .calendar-nav span{display:none}
  .booking-calendar-legend{padding:10px 12px}
  .booking-calendar-weekday{padding:8px 2px;font-size:.61rem}
  .booking-calendar-day{min-height:91px;padding:6px 4px}
  .booking-calendar-day time{width:22px;height:22px;margin-bottom:4px;font-size:.7rem}
  .calendar-event{display:block;padding:3px 4px;font-size:.59rem}
  .calendar-event-time{display:none}
}

/* Equipment booking handover and export */
.page-actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin:-10px 0 20px}.action-card{border-left:5px solid var(--purple)}.print-summary{max-width:1100px;margin:0 auto 18px}@media print{.topbar,.sidebar,.footer,.page-actions,.form-actions,.button{display:none!important}.content{margin:0!important;padding:0!important}.card,.table-wrap{box-shadow:none!important;border-color:#bbb!important}.print-summary{max-width:none!important}.content+.footer{margin:0!important}}


/* v1.9 — Equipment booking handover sheet. */
.print-sheet{max-width:1040px;margin:0 auto;background:#fff;border:1px solid #d7d3d0;border-radius:18px;overflow:hidden;box-shadow:0 12px 34px rgba(29,29,27,.08)}
.print-sheet-header{padding:28px 30px 24px;display:flex;align-items:flex-start;justify-content:space-between;gap:24px;border-bottom:5px solid var(--red);background:linear-gradient(135deg,#fff 0%,#fff8f6 100%)}
.print-brand{display:flex;align-items:center;gap:18px;min-width:0}.print-brand img{display:block;width:176px;height:50px;max-width:38vw;object-fit:contain;object-position:left center}.print-brand-mark{display:grid;place-items:center;width:48px;height:48px;border-radius:50%;background:var(--red);color:#fff;font-size:26px}.print-group-name{margin:0 0 2px;color:var(--red);font-size:.85rem;font-weight:900;text-transform:uppercase;letter-spacing:.065em}.print-brand h1{margin:0;font-size:1.72rem;line-height:1.08;font-weight:900}.print-brand p:last-child{margin:5px 0 0;color:var(--muted);font-weight:700}.print-reference{text-align:right;flex:0 0 auto}.print-reference span{display:block;font-size:.74rem;color:var(--muted);font-weight:900;text-transform:uppercase;letter-spacing:.06em}.print-reference strong{display:block;margin-top:3px;font-family:ui-monospace,SFMono-Regular,Consolas,monospace;font-size:1.05rem;color:var(--navy)}
.print-summary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0;border-bottom:1px solid var(--line)}.print-summary-grid>div{padding:16px 20px;border-right:1px solid var(--line);border-bottom:1px solid var(--line);min-height:78px}.print-summary-grid>div:nth-child(3n){border-right:0}.print-summary-grid>div:nth-last-child(-n+3){border-bottom:0}.print-summary-grid strong{display:block;font-size:.98rem;line-height:1.25}.print-summary-grid small{display:block;margin-top:2px}.print-holder{display:flex;gap:8px;align-items:baseline;padding:13px 20px;background:#f9f9fa;border-bottom:1px solid var(--line)}.print-holder .label{margin:0}.print-holder span:last-child{color:var(--muted);font-size:.88rem}
.print-table-wrap{padding:24px 20px 0}.print-equipment-table{min-width:0;border:1px solid #bcb8b5;border-radius:8px;overflow:hidden}.print-equipment-table th{background:#f3f0ef;color:#262321;border-bottom:2px solid #9d9894;font-size:.73rem}.print-equipment-table td{height:56px;border-color:#d8d4d1}.print-equipment-table tbody tr:nth-child(even){background:#fcfbfb}.print-equipment-table .col-check{width:66px;text-align:center}.print-equipment-table .col-qty{width:90px;text-align:center}.check-cell{text-align:center;vertical-align:middle}.check-box{display:inline-block;width:22px;height:22px;border:2px solid #322f2d;border-radius:3px;background:#fff}.asset-id-print{font-family:ui-monospace,SFMono-Regular,Consolas,monospace;font-weight:900;color:var(--navy);white-space:nowrap}.qty-cell{text-align:center;font-weight:900;font-size:1rem}.condition-line{display:inline-block;min-width:120px;padding-bottom:3px;border-bottom:1px dashed #8d8884;font-weight:700}.print-equipment-table small{display:block;margin-top:2px}
.print-notes{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding:22px 20px 0}.write-area{min-height:95px;border:1px solid #bcb8b5;border-radius:6px;background:repeating-linear-gradient(to bottom,#fff 0,#fff 25px,#e3dfdc 26px,#fff 27px)}.print-signatures{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;padding:22px 20px}.signature-line{height:34px;border-bottom:1px solid #5d5855}.print-footer-note{margin:0;padding:14px 20px 18px;color:#514c49;font-size:.8rem;border-top:1px solid var(--line);background:#fbfaf9}.print-actions{max-width:1040px;margin:18px auto 0}
@media(max-width:760px){.print-sheet-header{padding:20px;display:block}.print-reference{text-align:left;margin-top:18px}.print-summary-grid{grid-template-columns:1fr 1fr}.print-summary-grid>div:nth-child(3n){border-right:1px solid var(--line)}.print-summary-grid>div:nth-child(2n){border-right:0}.print-summary-grid>div:nth-last-child(-n+3){border-bottom:1px solid var(--line)}.print-summary-grid>div:nth-last-child(-n+2){border-bottom:0}.print-table-wrap{padding:18px 12px 0}.print-notes,.print-signatures{grid-template-columns:1fr;padding-left:12px;padding-right:12px}.print-brand img{max-width:55vw}}
/* v1.10 — Portrait A4 print layout for equipment handover sheets. */
@media print{
  @page{size:A4 portrait;margin:8mm}
  body{background:#fff;font-size:8.6pt;line-height:1.25}
  .topbar,.sidebar,.footer,.alert,.page-heading,.print-actions{display:none!important}
  .content,.content+.footer{margin:0!important;padding:0!important;min-height:0!important}
  .print-sheet{width:100%;max-width:none;margin:0;border:0;border-radius:0;box-shadow:none;overflow:visible}
  .print-sheet-header{padding:0 0 9px;border-bottom-width:3px;gap:14px}
  .print-brand{gap:10px}.print-brand img{width:124px;height:36px}.print-brand-mark{width:36px;height:36px;font-size:20px}
  .print-group-name{font-size:7.2pt;margin-bottom:1px}.print-brand h1{font-size:14pt}.print-brand p:last-child{margin-top:2px;font-size:8pt}
  .print-reference span{font-size:7.2pt}.print-reference strong{font-size:9.4pt}
  .print-summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  .print-summary-grid>div{padding:6px 8px;min-height:0;border-right:1px solid var(--line)!important;border-bottom:1px solid var(--line)!important}
  .print-summary-grid>div:nth-child(2n){border-right:0!important}
  .print-summary-grid>div:nth-last-child(-n+2){border-bottom:0!important}
  .print-summary-grid strong{font-size:8.8pt}.print-summary-grid small{font-size:7.7pt}
  .print-holder{padding:7px 8px;font-size:8pt}
  .print-table-wrap{padding:10px 0 0}
  .print-equipment-table{width:100%;table-layout:fixed;font-size:7.8pt;border-collapse:collapse}
  .print-equipment-table th,.print-equipment-table td{padding:5px 5px;vertical-align:middle;overflow-wrap:anywhere}
  .print-equipment-table th{font-size:7.2pt;line-height:1.1}.print-equipment-table td{height:31px}
  .print-equipment-table th:nth-child(1),.print-equipment-table td:nth-child(1){width:8%;text-align:center}
  .print-equipment-table th:nth-child(2),.print-equipment-table td:nth-child(2){width:18%}
  .print-equipment-table th:nth-child(3),.print-equipment-table td:nth-child(3){width:43%}
  .print-equipment-table th:nth-child(4),.print-equipment-table td:nth-child(4){width:10%;text-align:center}
  .print-equipment-table th:nth-child(5),.print-equipment-table td:nth-child(5){width:21%}
  .print-equipment-table small{display:none}.check-box{width:15px;height:15px;border-width:1.5px}.asset-id-print{font-size:7.4pt}.qty-cell{font-size:8.6pt}.condition-line{min-width:0;width:100%;font-size:7.5pt;line-height:1.15}
  .print-notes{padding:10px 0 0;gap:10px}.write-area{min-height:44px}.print-signatures{padding:10px 0;gap:10px}.signature-line{height:22px}
  .print-footer-note{padding:7px 0 0;background:#fff;border-top:0;font-size:7.2pt}.badge{border:1px solid #888;background:#fff!important;color:#111!important;padding:1px 4px}
  .print-sheet-header,.print-summary-grid,.print-holder,.print-equipment-table tr,.print-notes,.print-signatures{break-inside:avoid;page-break-inside:avoid}
}

/* v1.14 — regular term-time meetings and controlled record deletion. */
.booking-manager-actions{justify-content:flex-start;margin:-8px 0 18px}
.page-actions form{margin:0}
.destructive-actions{justify-content:flex-end;margin:-8px 0 18px}
.recurring-help{border-left:5px solid var(--purple);background:linear-gradient(135deg,#fff 0%,#fbf8ff 100%);margin-bottom:20px}
.recurring-help h2{margin-bottom:5px}
.recurring-help p{margin:0;max-width:850px;color:var(--muted)}
.recurring-guide{background:#fbfcff;border-color:#dbe5f0}
.recurring-guide .vertical-dl{margin-top:8px}
.recurring-guide .vertical-dl>div{padding:9px 0;border-bottom:1px solid #e5ebf2}
.recurring-guide .vertical-dl>div:last-child{border-bottom:0}
.recurring-guide dd{margin:2px 0 0;font-weight:800}
@media(max-width:760px){.booking-manager-actions,.destructive-actions{justify-content:stretch}.booking-manager-actions .button,.destructive-actions .button{width:100%;text-align:center}}
