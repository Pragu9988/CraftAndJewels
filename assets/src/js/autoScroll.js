// Import Splide JavaScript and AutoScroll extension
import Splide from "@splidejs/splide";
import { AutoScroll } from "@splidejs/splide-extension-auto-scroll";

document.addEventListener("DOMContentLoaded", () => {

  // Initialize the Hero Slider
  const heroSliderElement = document.querySelector('.ht-splide-hero');
  if (heroSliderElement) {
    new Splide(heroSliderElement, {
      type: 'fade',
      rewind: true,
      perPage: 1,
      autoplay: true,
      interval: 5000,
      arrows: false,
      pagination: true,
      speed: 1200,
      easing: 'cubic-bezier(0.25, 1, 0.5, 1)'
    }).mount();
  }

  // Initialize Category Slider (ht-cat-slider) — slide on click
  const catSliders = document.querySelectorAll(".ht-cat-slider__content");
  catSliders.forEach(sliderElement => {
    new Splide(sliderElement, {
      type: 'slide',
      perPage: 7,
      gap: '2.5rem',
      arrows: true,
      pagination: false,
      drag: true,
      speed: 800,
      easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
      breakpoints: {
        1200: {
          perPage: 7,
          gap: '1.5rem',
        },
        992: {
          perPage: 5,
          gap: '1.5rem',
        },
        768: {
          perPage: 3,
          gap: '1rem',
        },
        480: {
          perPage: 2,
          gap: '1rem',
        }
      }
    }).mount();
  });

  // Initialize Heritage Assurance Slider — continuous auto-scroll
  const assuranceSliders = document.querySelectorAll(".ht-assurance-slider");
  assuranceSliders.forEach(sliderElement => {
    new Splide(sliderElement, {
      type: 'loop',
      drag: 'free',
      focus: 'center',
      perPage: 1,
      autoWidth: true,
      gap: '0',
      arrows: false,
      pagination: false,
      autoScroll: {
        speed: .5,
        pauseOnHover: true,
        pauseOnFocus: false,
      },
    }).mount({ AutoScroll });
  });

  // Initialize all other standard Splide sliders
  const defaultSliders = document.querySelectorAll(".splide:not(.ht-splide-hero):not(.ht-cat-slider__content)");

  defaultSliders.forEach(sliderElement => {
    new Splide(sliderElement, {
      type: 'loop',
      autoScroll: {
        speed: 0.9,
      },
    }).mount({ AutoScroll });
  });

  console.log("Sliders Initialized");
});