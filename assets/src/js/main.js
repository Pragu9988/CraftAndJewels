/**
 * CSS Files
 */
import "../scss/main.scss";
import "@fortawesome/fontawesome-free/css/all.css";

import "@splidejs/splide/dist/css/splide.min.css";
import Splide from "@splidejs/splide";
import { AutoScroll } from "@splidejs/splide-extension-auto-scroll";

/**
 * JS Files
 */
// import "./nav";
// import "./gsap";
// import "./vacancy";
// import "./animation";
// import "./scroll-top";
// import "./loader"

import "./navigation";
import "./shop-filters";
import "./single-acordian";
import "./tab";
import "./slider-animation";
import "./faq";

window.dataLayer = window.dataLayer || [];
function gtag() { dataLayer.push(arguments); }
gtag('js', new Date());

gtag('config', 'G-FH17E5ZJB5');
console.log("Deepen Main page");


AOS.init({
    duration: 800,
    easing: "ease-out-cubic",
    once: true
    // once: true,
    // mirror: false,
    // anchorPlacement: "top-bottom"
});
