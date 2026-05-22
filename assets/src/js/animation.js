// document.addEventListener("DOMContentLoaded", () => {
//    const observer = new IntersectionObserver(
//       (entries) => {
//          entries.forEach(entry => {
//             // If the section enters the view, go to 100% opacity
//             if (entry.isIntersecting) {
//                entry.target.classList.add('kl-visible');
//             } else {
//                // If it leaves the view, go back to 10% opacity
//                entry.target.classList.remove('kl-visible');
//             }
//          });
//       },
//       {
//          /* 0.3 means the animation triggers when 30% of the 
//             container is visible. This prevents flickering.
//          */
//          threshold: 0.6
//       }
//    );

//    document.querySelectorAll('section .kl-container').forEach(container => {
//       observer.observe(container);
//    });
// });

// document.addEventListener("DOMContentLoaded", () => {
//    if (window.innerWidth < 1024) return; // skip on mobile/tablet

//    const heading = document.querySelector(".animate-up");
//    if (!heading) return;

//    const text = heading.innerText;
//    heading.innerText = "";

//    text.split("").forEach((char, index) => {
//       const span = document.createElement("span");
//       span.innerText = char === " " ? "\u00A0" : char;
//       heading.appendChild(span);

//       setTimeout(() => {
//          span.classList.add("active");
//       }, index * 30);
//    });
// });



// // --- THE JAVASCRIPT ---
// document.addEventListener("DOMContentLoaded", () => {
//    const heading = document.querySelector(".animate-up");
//    const text = heading.innerText;

//    // Clear text
//    heading.innerText = "";

//    // Split text into spans
//    text.split("").forEach((char, index) => {
//       const span = document.createElement("span");
//       span.innerText = char === " " ? "\u00A0" : char;
//       heading.appendChild(span);

//       // Add the 'active' class with a stagger delay
//       setTimeout(() => {
//          span.classList.add("active");
//       }, index * 30); // 60ms delay per letter
//    });
// });

