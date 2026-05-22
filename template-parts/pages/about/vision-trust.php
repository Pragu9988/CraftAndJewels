<style>
/* ── SCOPE: All styles prefixed .hcj so they
   never conflict with your WP theme ── */

.hcj *, .hcj *::before, .hcj *::after {
  margin:0; padding:0; box-sizing:border-box;
}
.hcj {
  /* Brand Tokens — matches craftandjewels.com */
  --maroon:      #631525;
  --maroon-deep: #3d0d17;
  --maroon-mid:  #8b2035;
  --maroon-pale: #f5eaec;
  --gold:        #c49848;
  --gold-lt:     #d9b870;
  --gold-dim:    #8a6b2e;
  --cream:       #f9f5ef;
  --warm:        #f0e8d8;
  --ivory:       #fdfaf6;
  --text:        #231a12;
  --text-mid:    #5a4e44;
  --text-muted:  #9a8e82;
  --rule:        rgba(196,152,72,.25);

  font-family: 'Jost', sans-serif;
  font-weight: 300;
  background: var(--cream);
  color: var(--text);
  overflow-x: hidden;
}

/* ── SCROLL REVEALS ── */
.hcj .rv    { opacity:0; transform:translateY(32px);
              transition:opacity .95s cubic-bezier(.22,1,.36,1),transform .95s cubic-bezier(.22,1,.36,1); }
.hcj .rv-l  { opacity:0; transform:translateX(-42px);
              transition:opacity 1.05s cubic-bezier(.22,1,.36,1),transform 1.05s cubic-bezier(.22,1,.36,1); }
.hcj .rv-r  { opacity:0; transform:translateX(42px);
              transition:opacity 1.05s cubic-bezier(.22,1,.36,1),transform 1.05s cubic-bezier(.22,1,.36,1); }
.hcj .rv.on, .hcj .rv-l.on, .hcj .rv-r.on { opacity:1; transform:none; }
.hcj .d1 { transition-delay:.12s; }
.hcj .d2 { transition-delay:.26s; }
.hcj .d3 { transition-delay:.42s; }
.hcj .d4 { transition-delay:.58s; }

/* ── HERO BANNER ── */
.hcj-hero {
  position:relative;
  height:72vh; min-height:520px;
  display:flex; align-items:center; justify-content:center;
  overflow:hidden; text-align:center;
}
.hcj-hero-bg {
  position:absolute; inset:0;
  background:url('https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=1800&q=85&auto=format&fit=crop')
    center 40% / cover no-repeat;
  transform:scale(1.06);
  transition:transform 7s ease-out;
}
.hcj-hero-bg.rdy { transform:scale(1); }
.hcj-hero-veil {
  position:absolute; inset:0;
  background:linear-gradient(
    165deg,
    rgba(61,13,23,.78) 0%,
    rgba(99,21,37,.72) 50%,
    rgba(30,6,12,.88) 100%
  );
}
/* Fine grain texture overlay */
.hcj-hero-grain {
  position:absolute; inset:0; pointer-events:none; opacity:.045;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size:160px;
}
/* Decorative gold border inset */
.hcj-hero::after {
  content:'';
  position:absolute; inset:1.5rem;
  border:1px solid rgba(196,152,72,.3);
  pointer-events:none; z-index:3;
}
.hcj-hero-content {
  position:relative; z-index:4;
  padding:2rem;
}
.hcj-hero-tag {
  display:inline-flex; align-items:center; gap:.75rem;
  font-size:.6rem; letter-spacing:.4em; text-transform:uppercase;
  color:var(--gold-lt); margin-bottom:1.6rem;
  opacity:0; animation:hcjUp .9s .3s cubic-bezier(.22,1,.36,1) forwards;
}
.hcj-hero-tag::before, .hcj-hero-tag::after {
  content:''; display:block; width:28px; height:1px; background:var(--gold);
}
.hcj-hero h1 {
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(2.8rem,7vw,5.8rem);
  font-weight:300; line-height:1.06; color:#fdf8f2;
  margin-bottom:1.4rem;
  opacity:0; animation:hcjUp 1s .5s cubic-bezier(.22,1,.36,1) forwards;
}
.hcj-hero h1 em { font-style:italic; color:var(--gold-lt); }
.hcj-hero-sub {
  font-size:.88rem; font-weight:300; line-height:1.95;
  color:rgba(253,248,242,.72); max-width:480px; margin:0 auto;
  opacity:0; animation:hcjUp 1s .72s cubic-bezier(.22,1,.36,1) forwards;
}
/* Scroll arrow */
.hcj-scroll-arrow {
  position:absolute; bottom:2rem; left:50%; transform:translateX(-50%);
  display:flex; flex-direction:column; align-items:center; gap:.4rem;
  opacity:0; animation:hcjFade 1s 1.4s ease forwards; z-index:4;
}
.hcj-scroll-arrow span {
  font-size:.52rem; letter-spacing:.28em; text-transform:uppercase;
  color:rgba(196,152,72,.7);
}
.hcj-arrow-line {
  width:1px; height:44px;
  background:linear-gradient(var(--gold),transparent);
  animation:hcjArrow 2s 1.8s ease-in-out infinite;
}
@keyframes hcjUp   { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
@keyframes hcjFade { from{opacity:0} to{opacity:1} }
@keyframes hcjArrow{
  0%,100%{transform:scaleY(1);opacity:.9}
  50%{transform:scaleY(.5);opacity:.3}
}

/* ── MARQUEE STRIP ── */
.hcj-marquee {
  background:var(--maroon);
  padding:.8rem 0; overflow:hidden; white-space:nowrap;
}
.hcj-marquee-track {
  display:inline-flex;
  animation:hcjScroll 24s linear infinite;
}
.hcj-marquee-track s {
  text-decoration:none;
  font-size:.58rem; font-weight:500;
  letter-spacing:.28em; text-transform:uppercase;
  color:rgba(253,248,242,.85); padding:0 2rem;
}
.hcj-marquee-track s.dot::after {
  content:'✦'; padding-left:2rem;
  color:var(--gold); font-size:.5rem;
}
@keyframes hcjScroll {
  from{transform:translateX(0)} to{transform:translateX(-50%)}
}

/* ── SECTION UTILITY ── */
.hcj-wrap  { max-width:1160px; margin:0 auto; padding:0 clamp(1.5rem,5vw,4rem); }
.hcj-label {
  font-size:.58rem; font-weight:500; letter-spacing:.4em;
  text-transform:uppercase; color:var(--gold-dim); display:block;
  margin-bottom:1rem;
}
.hcj-rule  { width:42px; height:1px; background:var(--gold); margin-bottom:1.8rem; }
.hcj-h2 {
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.9rem,3.8vw,3rem);
  font-weight:300; line-height:1.15; color:var(--maroon);
}
.hcj-h2 em { font-style:italic; color:var(--maroon-mid); }
.hcj-body  {
  font-size:.9rem; font-weight:300;
  line-height:1.95; color:var(--text-mid);
  margin-bottom:1rem;
}

/* ── WHO WE ARE ── */
.hcj-who {
  display:grid; grid-template-columns:1fr 1fr;
  background:var(--ivory);
}
.hcj-who-img {
  position:relative; overflow:hidden; min-height:560px;
}
.hcj-who-img img {
  position:absolute; inset:0; width:100%; height:100%;
  object-fit:cover; display:block;
  transition:transform 7s ease;
}
.hcj-who:hover .hcj-who-img img { transform:scale(1.05); }
/* Gold frame */
.hcj-who-img-frame {
  position:absolute; top:1.8rem; left:1.8rem;
  right:-1.8rem; bottom:-1.8rem;
  border:1px solid rgba(196,152,72,.35);
  pointer-events:none; z-index:2;
}
/* Maroon corner accent */
.hcj-who-img::before {
  content:'';
  position:absolute; top:0; left:0;
  width:5px; height:80px;
  background:var(--maroon); z-index:3;
}
.hcj-who-text {
  background:var(--ivory);
  padding:clamp(3rem,6vw,6rem) clamp(2.5rem,5vw,5rem);
  display:flex; flex-direction:column; justify-content:center;
}
/* Stat row */
.hcj-stats {
  display:flex; gap:2.5rem;
  padding-top:2rem; margin-top:2.2rem;
  border-top:1px solid var(--rule);
}
.hcj-stat-n {
  font-family:'Cormorant Garamond',serif;
  font-size:2.8rem; font-weight:300;
  line-height:1; color:var(--maroon);
}
.hcj-stat-l {
  font-size:.58rem; letter-spacing:.2em;
  text-transform:uppercase; color:var(--text-muted);
  margin-top:.35rem;
}

/* ── MILESTONE BAND (maroon) ── */
.hcj-miles {
  background:var(--maroon-deep);
  padding:4.5rem clamp(1.5rem,5vw,4rem);
  display:flex; align-items:center;
  justify-content:space-between;
  gap:3rem; flex-wrap:wrap;
}
.hcj-miles-quote {
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.4rem,2.8vw,2.1rem);
  font-style:italic; font-weight:300;
  color:rgba(253,248,242,.9);
  max-width:580px; line-height:1.5;
}
.hcj-miles-nums {
  display:flex; gap:3rem; flex-shrink:0;
}
.hcj-miles-item { text-align:center; }
.hcj-miles-n {
  font-family:'Cormorant Garamond',serif;
  font-size:3.2rem; font-weight:300;
  color:var(--gold-lt); line-height:1;
}
.hcj-miles-l {
  font-size:.58rem; letter-spacing:.22em;
  text-transform:uppercase; color:rgba(253,248,242,.5);
  margin-top:.4rem;
}

/* ── VISION + MISSION ── */
.hcj-vm { background:var(--cream); padding:7rem clamp(1.5rem,5vw,4rem); }
.hcj-vm-hdr { text-align:center; margin-bottom:4.5rem; }
.hcj-vm-hdr .hcj-label { justify-content:center; display:flex; }
.hcj-vm-hdr .hcj-rule  { margin:0 auto 1.8rem; }
.hcj-vm-cards { display:grid; grid-template-columns:1fr 1fr; gap:1.5px; }
.hcj-vm-card {
  background:var(--ivory); padding:3.5rem clamp(2rem,4vw,3.5rem);
  position:relative; overflow:hidden;
  transition:background .35s;
}
.hcj-vm-card:hover { background:#fff; }
/* Animated top border reveal */
.hcj-vm-card::before {
  content:'';
  position:absolute; top:0; left:0; right:0;
  height:3px; background:var(--maroon);
  transform:scaleX(0); transform-origin:left;
  transition:transform .55s cubic-bezier(.22,1,.36,1);
}
.hcj-vm-card:hover::before { transform:scaleX(1); }
/* Large decorative glyph */
.hcj-vm-glyph {
  font-family:'Cormorant Garamond',serif;
  font-size:6rem; line-height:1;
  color:rgba(99,21,37,.07);
  position:absolute; top:1rem; right:2rem;
  pointer-events:none; user-select:none;
}
.hcj-vm-tag {
  font-size:.56rem; letter-spacing:.38em;
  text-transform:uppercase; color:var(--gold-dim);
  display:block; margin-bottom:.9rem;
}
.hcj-vm-card-h {
  font-family:'Cormorant Garamond',serif;
  font-size:1.75rem; font-weight:400;
  color:var(--maroon); margin-bottom:1.2rem;
}
.hcj-vm-card p {
  font-size:.88rem; font-weight:300;
  line-height:1.95; color:var(--text-mid);
}
/* Icon line top */
.hcj-vm-icon {
  width:38px; height:38px; margin-bottom:1.5rem;
  display:flex; align-items:center; justify-content:center;
  border:1px solid var(--rule); border-radius:50%;
  color:var(--gold); font-size:1rem;
}

/* ── CRAFT IMAGE STRIP ── */
.hcj-craft {
  position:relative; min-height:500px;
  display:flex; align-items:center; overflow:hidden;
}
.hcj-craft-bg {
  position:absolute; inset:0;
  background:url('https://craftandjewels.com/wp-content/uploads/2026/05/about-heritage-work.webp')
    center 45% / cover no-repeat;
  transition:transform 7s ease;
}
.hcj-craft:hover .hcj-craft-bg { transform:scale(1.04); }
.hcj-craft-veil {
  position:absolute; inset:0;
  background:linear-gradient(95deg,
    rgba(61,13,23,.93) 0%,
    rgba(61,13,23,.75) 55%,
    rgba(61,13,23,.15) 100%);
}
.hcj-craft-text {
  position:relative; z-index:2;
  padding:5.5rem clamp(2rem,6vw,6rem);
  max-width:560px;
}
.hcj-craft-text .hcj-label { color:var(--gold-lt); }
.hcj-craft-text .hcj-rule  { background:var(--gold); }
.hcj-craft-h {
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(2rem,4.2vw,3.4rem);
  font-weight:300; line-height:1.12;
  color:#fdf8f2; margin-bottom:1.5rem;
}
.hcj-craft-h em { font-style:italic; color:var(--gold-lt); }
.hcj-craft-body {
  font-size:.88rem; font-weight:300;
  line-height:1.95; color:rgba(253,248,242,.7);
}

/* ── GOALS ── */
.hcj-goals { background:var(--warm); padding:7rem clamp(1.5rem,5vw,4rem); }
.hcj-goals-hdr { margin-bottom:4rem; }
.hcj-goals-grid {
  display:grid; grid-template-columns:repeat(3,1fr);
  gap:1.5px;
}
.hcj-goal {
  background:var(--cream);
  padding:3rem 2.2rem;
  position:relative; overflow:hidden;
  transition:background .35s, box-shadow .35s;
  cursor:default;
}
.hcj-goal:hover {
  background:#fff;
  box-shadow:0 8px 40px rgba(99,21,37,.08);
}
/* Gold bottom slide */
.hcj-goal::after {
  content:'';
  position:absolute; bottom:0; left:0; right:0;
  height:2px; background:var(--gold);
  transform:scaleX(0); transform-origin:left;
  transition:transform .5s cubic-bezier(.22,1,.36,1);
}
.hcj-goal:hover::after { transform:scaleX(1); }
/* Big number BG */
.hcj-goal-bg-n {
  position:absolute; bottom:-1rem; right:1rem;
  font-family:'Cormorant Garamond',serif;
  font-size:7rem; font-weight:300;
  color:rgba(99,21,37,.05); line-height:1;
  pointer-events:none; user-select:none;
}
.hcj-goal-num {
  font-family:'Cormorant Garamond',serif;
  font-size:.75rem; font-weight:600;
  letter-spacing:.18em; color:var(--gold-dim);
  display:block; margin-bottom:1.2rem;
}
.hcj-goal h4 {
  font-family:'Cormorant Garamond',serif;
  font-size:1.3rem; font-weight:400;
  color:var(--maroon); margin-bottom:.9rem;
  line-height:1.3;
}
.hcj-goal p {
  font-size:.83rem; font-weight:300;
  line-height:1.95; color:var(--text-mid);
}

/* ── ISO BADGE STRIP ── */
.hcj-iso {
  background:var(--ivory); border-top:1px solid var(--rule);
  padding:3rem clamp(1.5rem,5vw,4rem);
  display:flex; align-items:center;
  justify-content:center; gap:3rem;
  flex-wrap:wrap;
}
.hcj-iso-badge {
  display:flex; align-items:center; gap:1.2rem;
}
.hcj-iso-circle {
  width:60px; height:60px; border-radius:50%;
  border:1.5px solid var(--gold);
  display:flex; flex-direction:column;
  align-items:center; justify-content:center;
  flex-shrink:0;
}
.hcj-iso-circle span:first-child {
  font-size:.55rem; font-weight:500;
  letter-spacing:.15em; color:var(--gold-dim);
}
.hcj-iso-circle span:last-child {
  font-size:.62rem; font-weight:500;
  color:var(--maroon);
}
.hcj-iso-text p {
  font-size:.82rem; font-weight:300;
  color:var(--text-mid); line-height:1.7;
  max-width:320px;
}
.hcj-iso-text strong {
  font-family:'Cormorant Garamond',serif;
  font-size:1.1rem; font-weight:400;
  color:var(--maroon); display:block;
  margin-bottom:.3rem;
}
.hcj-divider {
  width:1px; height:60px; background:var(--rule);
}

/* ── CLOSING CTA ── */
.hcj-cta {
  background:var(--maroon);
  padding:5.5rem clamp(1.5rem,5vw,4rem);
  text-align:center;
  position:relative; overflow:hidden;
}
.hcj-cta::before {
  content:'"';
  position:absolute; top:-2rem; left:1.5rem;
  font-family:'Cormorant Garamond',serif;
  font-size:20rem; line-height:1;
  color:rgba(253,248,242,.04);
  pointer-events:none;
}
.hcj-cta-inner { position:relative; z-index:2; max-width:700px; margin:0 auto; }
.hcj-cta-tagline {
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.5rem,3.2vw,2.4rem);
  font-style:italic; font-weight:300;
  color:rgba(253,248,242,.95); margin-bottom:2.5rem;
  line-height:1.5;
}
.hcj-cta-btn {
  display:inline-block;
  padding:1rem 3rem;
  background:transparent;
  border:1px solid var(--gold);
  font-family:'Jost',sans-serif;
  font-size:.65rem; font-weight:500;
  letter-spacing:.28em; text-transform:uppercase;
  color:var(--gold-lt); text-decoration:none;
  transition:background .3s, color .3s;
  cursor:pointer;
}
.hcj-cta-btn:hover { background:var(--gold); color:var(--maroon-deep); }

/* ── RESPONSIVE ── */
@media (max-width:920px) {
  .hcj-who          { grid-template-columns:1fr; }
  .hcj-who-img      { min-height:55vw; position:relative; }
  .hcj-who-img-frame{ display:none; }
  .hcj-vm-cards     { grid-template-columns:1fr; }
  .hcj-goals-grid   { grid-template-columns:1fr; }
  .hcj-miles        { flex-direction:column; gap:2.5rem; }
  .hcj-miles-nums   { justify-content:center; }
}
@media (max-width:600px) {
  .hcj-stats        { flex-wrap:wrap; gap:1.5rem; }
  .hcj-iso          { flex-direction:column; text-align:center; }
  .hcj-divider      { width:60px; height:1px; }
}
</style>

<!-- ════════════════════════════════════════ -->
<!--   PASTE THIS ENTIRE BLOCK INTO WORDPRESS -->
<!-- ════════════════════════════════════════ -->
<div class="hcj">

<!-- ① HERO BANNER -->
<section class="hcj-hero">
  <div class="hcj-hero-bg" id="hcjBg"></div>
  <div class="hcj-hero-veil"></div>
  <div class="hcj-hero-grain"></div>
  <div class="hcj-hero-content">
    <p class="hcj-hero-tag">Heritage Craft &amp; Jewels</p>
    <h1>Our <em>Story &amp;<br>Heritage</em></h1>
    <p class="hcj-hero-sub">
      Born from Nepal's living traditions —<br>
      crafted with 25 years of artisan mastery.
    </p>
  </div>
  <div class="hcj-scroll-arrow" aria-hidden="true">
    <div class="hcj-arrow-line"></div>
    <span>Discover</span>
  </div>
</section>

<!-- ② MARQUEE -->
<div class="hcj-marquee" aria-hidden="true">
  <div class="hcj-marquee-track">
    <s class="dot">Handcrafted in Kathmandu</s>
    <s class="dot">ISO 9001:2015 Certified</s>
    <s class="dot">25 Years of Heritage</s>
    <s class="dot">Gold · Silver · Diamond</s>
    <s class="dot">Newar &amp; Mithila Traditions</s>
    <s class="dot">Gulf · American · European Influence</s>
    <s class="dot">Handcrafted in Kathmandu</s>
    <s class="dot">ISO 9001:2015 Certified</s>
    <s class="dot">25 Years of Heritage</s>
    <s class="dot">Gold · Silver · Diamond</s>
    <s class="dot">Newar &amp; Mithila Traditions</s>
    <s class="dot">Gulf · American · European Influence</s>
  </div>
</div>

<!-- ③ WHO WE ARE -->
<section class="hcj-who">
  <!-- Image -->
  <div class="hcj-who-img hcj-rv-l" id="whoImg">
    <img
      src="https://craftandjewels.com/wp-content/uploads/2026/05/1972-our-work.webp"
      alt="Heritage Craft & Jewels artisan at work"
      loading="lazy"
    >
    <div class="hcj-who-img-frame"></div>
  </div>
  <!-- Text -->
  <div class="hcj-who-text">
    <span class="hcj-label rv d1">Our Story</span>
    <h2 class="hcj-h2 rv d2">Who <em>We Are</em></h2>
    <div class="hcj-rule rv d2"></div>
    <p class="hcj-body rv d3">
      Welcome to Heritage Craft &amp; Jewels — where tradition meets craftsmanship, 
      and culture shines through every piece. We are a professional, energetic, and 
      dynamic team committed to delivering international-level quality and purity 
      in every design we create.
    </p>
    <p class="hcj-body rv d3">
      Our journey began 25 years ago, growing alongside jewellery expertise from 
      Gulf countries while incorporating both American and European sensibilities. 
      Each piece in our collection is inspired by the rich traditions and culture 
      of Nepal — meticulously handcrafted by artisans whose skill is their legacy.
    </p>
    <div class="hcj-stats rv d4">
      <div>
        <div class="hcj-stat-n" data-count="25">0</div>
        <div class="hcj-stat-l">Years of Heritage</div>
      </div>
      <div>
        <div class="hcj-stat-n" data-count="3">0</div>
        <div class="hcj-stat-l">Cultural Traditions</div>
      </div>
      <div>
        <div class="hcj-stat-n">Int'l</div>
        <div class="hcj-stat-l">Quality Standard</div>
      </div>
    </div>
  </div>
</section>

<!-- ④ MILESTONE BAND -->
<div class="hcj-miles">
  <p class="hcj-miles-quote rv">
    "Each piece is a bridge between Nepal's living heritage and 
     the world — handcrafted, hallmarked, and carried with pride."
  </p>
  <div class="hcj-miles-nums rv d2">
    <div class="hcj-miles-item">
      <div class="hcj-miles-n" data-count="25">0</div>
      <div class="hcj-miles-l">Years of craft</div>
    </div>
    <div class="hcj-miles-item">
      <div class="hcj-miles-n" data-count="3">0</div>
      <div class="hcj-miles-l">Metal types</div>
    </div>
    <div class="hcj-miles-item">
      <div class="hcj-miles-n">100%</div>
      <div class="hcj-miles-l">Authenticity</div>
    </div>
  </div>
</div>

<!-- ⑤ WHAT WE DO: VISION + MISSION -->
<section class="hcj-vm">
  <div class="hcj-wrap">
    <div class="hcj-vm-hdr">
      <span class="hcj-label rv">What We Do</span>
      <div class="hcj-rule rv d1"></div>
      <h2 class="hcj-h2 rv d1" style="font-size:clamp(1.9rem,3.5vw,2.8rem);">
        Our <em>Purpose &amp; Promise</em>
      </h2>
    </div>
    <div class="hcj-vm-cards">
      <!-- Vision -->
      <div class="hcj-vm-card rv-l">
        <div class="hcj-vm-glyph">◈</div>
        <div class="hcj-vm-icon">◇</div>
        <span class="hcj-vm-tag">Our Vision</span>
        <h3 class="hcj-vm-card-h">Quality, Elegance &amp; Community</h3>
        <p>
          To deliver quality, craftsmanship, and elegance wrapped in an experience 
          that feels personal and unique. We are committed to fostering an environment 
          where our customers, artisans, and communities can thrive, evolve, and grow 
          together — while offering antique designs crafted for the individual.
        </p>
      </div>
      <!-- Mission -->
      <div class="hcj-vm-card rv-r">
        <div class="hcj-vm-glyph">◇</div>
        <div class="hcj-vm-icon">⟡</div>
        <span class="hcj-vm-tag">Our Mission</span>
        <h3 class="hcj-vm-card-h">Connect You to Your Roots</h3>
        <p>
          To craft and design jewellery that connects you to your roots and 
          invites you to explore the enduring beauty of Nepal's rich cultural 
          heritage. Every ring, pendant, and necklace is a living bridge between 
          the ancient and the contemporary.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ⑥ CRAFT PARALLAX STRIP -->
<section class="hcj-craft">
  <div class="hcj-craft-bg"></div>
  <div class="hcj-craft-veil"></div>
  <div class="hcj-craft-text">
    <span class="hcj-label rv">The Craft</span>
    <h2 class="hcj-craft-h rv d1">
      From Nepal's<br><em>Hands to Yours</em>
    </h2>
    <div class="hcj-rule rv d1"></div>
    <p class="hcj-craft-body rv d2">
      Every creation passes through artisan hands trained in Newar, Kirat, 
      and Mithila goldsmithing traditions — centuries-old techniques 
      meeting contemporary desire. Our goldsmiths bring Gulf, American, 
      and European design influences into every unique piece.
    </p>
  </div>
</section>

<!-- ⑦ OUR GOALS -->
<section class="hcj-goals">
  <div class="hcj-wrap">
    <div class="hcj-goals-hdr">
      <span class="hcj-label rv">Our Goals</span>
      <h2 class="hcj-h2 rv d1">Three Pillars of Our <em>Commitment</em></h2>
    </div>
    <div class="hcj-goals-grid">
      <div class="hcj-goal rv">
        <div class="hcj-goal-bg-n">01</div>
        <span class="hcj-goal-num">01 — Innovation</span>
        <h4>Precision &amp; Design Leadership</h4>
        <p>
          Staying at the forefront of design trends through rigorous quality 
          and accuracy checking of diamond, gold, and silver — delivering 
          innovative, desirable collections from our manufacturing process 
          directly to you.
        </p>
      </div>
      <div class="hcj-goal rv d1">
        <div class="hcj-goal-bg-n">02</div>
        <span class="hcj-goal-num">02 — Tradition</span>
        <h4>Heritage Meets Contemporary</h4>
        <p>
          Honouring age-old traditions by blending them with contemporary 
          design — offering a collection that celebrates diversification 
          and cultural enrichment across Nepal's rich jewellery heritage.
        </p>
      </div>
      <div class="hcj-goal rv d2">
        <div class="hcj-goal-bg-n">03</div>
        <span class="hcj-goal-num">03 — Authenticity</span>
        <h4>A Lifelong Commitment</h4>
        <p>
          A lifelong commitment to authenticity in every piece, every 
          process, and every relationship — with our customers, our 
          artisans, and the living culture that inspires us every day.
        </p>
      </div>
    </div>
  </div>
</section>
	
<!-- ⑨ CLOSING CTA -->
<section class="hcj-cta">
  <div class="hcj-cta-inner">
    <p class="hcj-cta-tagline rv">
      "We don't just make jewellery — we preserve stories, 
       honour heritage, and craft connections that last a lifetime."
    </p>
    <a href="/shop-all" class="hcj-cta-btn rv d1">
      Explore the Collection
    </a>
  </div>
</section>

<!-- ⑧ ISO TRUST STRIP -->
<div class="hcj-iso">
  <div class="hcj-iso-badge rv-l">
    <div class="hcj-iso-circle">
      <span>ISO</span>
      <span>9001</span>
    </div>
    <div class="hcj-iso-text">
      <strong>ISO 9001:2015 Certified</strong>
      <p>Our quality management systems meet the highest international 
         standards — from raw material to finished jewellery.</p>
    </div>
  </div>
  <div class="hcj-divider" aria-hidden="true"></div>
  <div class="hcj-iso-badge rv-r">
    <div class="hcj-iso-circle">
      <span>25</span>
      <span>Years</span>
    </div>
    <div class="hcj-iso-text">
      <strong>25 Years of Master Craft</strong>
      <p>Two decades of refining our artisan process — 
         building trust with every customer across Nepal and beyond.</p>
    </div>
  </div>
</div>


</div><!-- /.hcj -->

<script>
(function(){
  /* Hero bg scale in */
  var bg = document.getElementById('hcjBg');
  if(bg) setTimeout(function(){ bg.classList.add('rdy'); }, 80);

  /* Scroll reveal */
  var items = document.querySelectorAll('.hcj .rv, .hcj .rv-l, .hcj .rv-r');
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(e.isIntersecting){
        e.target.classList.add('on');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });
  items.forEach(function(el){ io.observe(el); });

  /* Animated counters */
  var counters = document.querySelectorAll('.hcj [data-count]');
  var cio = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(!e.isIntersecting) return;
      var el = e.target;
      var target = parseInt(el.getAttribute('data-count'), 10);
      var start = null;
      var dur = 1600;
      function step(ts){
        if(!start) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.floor(eased * target) + '+';
        if(p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
      cio.unobserve(el);
    });
  }, { threshold: 0.5 });
  counters.forEach(function(c){ cio.observe(c); });
})();
</script>
