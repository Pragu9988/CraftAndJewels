import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/all";

gsap.registerPlugin(ScrollTrigger);

// Target the blocks in the first column
const blocks = gsap.utils.toArray(".kl-people__content-wrap");

// Target the images in the second column
const images = gsap.utils.toArray(".kl-people__thumbnail img");

// Set up ScrollTrigger for the blocks
blocks.forEach((block, index) => {
  ScrollTrigger.create({
    trigger: block,
    start: "top 20%", // Adjust the start position as needed
    end: "bottom 90%", // Adjust the end position as needed
    onEnter: () => {
      // Reset all image opacities to 0
      gsap.set(images, { opacity: 0, scale: 0.8, x: 50, duration: 1 });

      // Get the corresponding image for the entered block
      const image = images[index];

      // Animate the image opacity to 1
      gsap.to(image, {
        opacity: 1,
        scale: 1,
        x: 50,
        y: 0,
        duration: 0.5,
        ease: "power2.out",
      });
    },
    onLeave: () => {
      // Reset all image opacities to 0
      gsap.set(images, { opacity: 0, scale: 0.8, x: 50, duration: 1 });
    },
    onEnterBack: () => {
      // Reset all image opacities to 0
      gsap.set(images, { opacity: 0, scale: 0.8, x: 50 });

      // Get the corresponding image for the entered block
      const image = images[index];

      // Animate the image opacity to 1
      gsap.to(image, {
        opacity: 1,
        scale: 1,
        x: 50,
        y: 0,
        duration: 0.5,
        ease: "power2.out",
      });
    },
    onLeaveBack: () => {
      // Reset all image opacities to 0
      gsap.set(images, { opacity: 0, scale: 0.8, x: 50, duration: 1 });
    },
  });
});

const logoOCTOWAYSAnimation = gsap.to("#text-OCTOWAYS", {
  scale: 0.6,
  duration: 0.3,
  ease: "power2.out",
  paused: true,
});

// Set up the GSAP animation for the logo
const logoAnimation = gsap.to("#text-labs", {
  scale: 0.6,
  x: -172,
  y: 32,
  duration: 0.3,
  ease: "power2.out",
  paused: true,
});

ScrollTrigger.create({
  trigger: ".site-header",
  start: "top top",
  end: 99999,
  toggleClass: {
    className: "is-sticky",
    targets: ".site-header",
  },
  onEnter: () => {
    logoOCTOWAYSAnimation.play();
    logoAnimation.play(); // Play the logo animation when the header is entered
  },
  onLeaveBack: () => {
    logoOCTOWAYSAnimation.reverse();
    logoAnimation.reverse(); // Reverse the logo animation when scrolling back up
  },
});

const sections = gsap.utils.toArray(".kl-home");
console.log(sections);
sections.forEach((section, index) => {
  gsap.to(section, {
    scrollTrigger: {
      trigger: ".kl-title",
      start: "top 0%",
      toggleActions: "animated fadeInLeft",
    },
    y: 0,
    opacity: 1,
    stagger: {
      amount: 1.5,
    },
    ease: "power1.inOutinOut",
  });
});

// ScrollTrigger.create({
//   trigger: ".kl-clients-marquee",
//   start: "top top", // Adjust the start position as needed
//   end: "bottom bottom", // Adjust the end position as needed
//   onEnter: () => {
//     // Reset all image opacities to 0
//     gsap.set(".kl-services__blurbs", {
//       opacity: 0,
//       scale: 0.8,
//       duration: 1,
//     });

//     // Get the corresponding image for the entered block

//     // Animate the image opacity to 1
//     gsap.to(".kl-services__blurbs", {
//       opacity: 1,
//       scale: 1,
//       duration: 1,
//       ease: "power2.out",
//     });
//   },
// });

document.addEventListener("DOMContentLoaded", function () {
  const bannerImage = document.querySelector(".kl-banner__image img");

  // Use GSAP to create the animation
  gsap.fromTo(
    bannerImage,
    { transformOrigin: "bottom right", scale: 0.9, opacity: 0 }, // Initial properties
    { scale: 1, opacity: 1, duration: 1, ease: "power2.out" } // Target properties and animation settings
  );
});


  gsap.registerPlugin(ScrollTrigger);

  gsap.from(".kl-blurb", {
    scrollTrigger: {
      trigger: ".kl-blurb-section",
      start: "top 80%",
      toggleActions: "play none none none"
    },
    y: 50,
    opacity: 0,
    duration: 0.5,
    stagger: 0.015
  });

  gsap.registerPlugin(ScrollTrigger);

  gsap.to(".kl-blurb", {
    scrollTrigger: {
      trigger: ".kl-services__blurbs",
      start: "top 80%",  // When the grid container reaches 80% of the viewport
      toggleActions: "play none none none",  // Play animation when the grid appears
      onEnter: () => startGridAnimation()  // Start animation when the first card enters the viewport
    },
    opacity: 1,
    x: 0,    // End position (final X value)
    y: 0,    // End position (final Y value)
    scale: 1,
    duration: 0.8,
    ease: "power3.out",
    stagger: {
      each: 0.2,
      from: "start"
    }
  });

  function startGridAnimation() {
    // Start animation from the top-left corner
    gsap.fromTo(".kl-blurb", {
      opacity: 0,
      x: 100,   // Start position (initial X value, move from left)
      y: 100,   // Start position (initial Y value, move from top)
      scale: 0.8
    }, {
      opacity: 1,
      x: 0,      // Final X position (move to normal position)
      y: 0,      // Final Y position (move to normal position)
      scale: 1,
      duration: 0.8,
      stagger: {
        each: 0.2,
        from: "start"
      },
      ease: "power3.out"
    });
  }