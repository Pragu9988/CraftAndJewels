/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**.html", "../**/**.php", "./src/js/**.js"],
  darkMode: true,
  theme: {
    extend: {
      borderRadius: {
        custom: "14px",
      },
      colors: {
        "primary-100": "var(--clr-primary-100)",
        "primary-200": "var(--clr-primary-200)",
        "primary-300": "var(--clr-primary-300)",
        "primary-400": "var(--clr-primary-400)",
        "primary-500": "var(--clr-primary-500)",
        "primary-600": "var(--clr-primary-600)",
        "secondary-100": "var(--clr-secondary-100)",
        "secondary-200": "var(--clr-secondary-200)",
        "secondary-300": "var(--clr-secondary-300)",
        "secondary-400": "var(--clr-secondary-400)",
        "secondary-500": "var(--clr-secondary-500)",
        "text-200": "var(--clr-text-200)",
        "text-400": "var(--clr-text-400)",
        "text-500": "var(--clr-text-500)",
        "acent-300": "var(--clr-accent-300)",
        "acent-400": "var(--clr-accent-400)",
        "acent-500": "var(--clr-accent-500)",
        "neutra-100": "var(--clr-neutral-100)",
        "neutra-200": "var(--clr-neutral-200)",
        "neutra-300": "var(--clr-neutral-300)",
        "neutra-400": "var(--clr-neutral-400)",
        "neutra-500": "var(--clr-neutral-500)",
        "neutra-600": "var(--clr-neutral-600)",
        "neutra-700": "var(--clr-neutral-700)",
        "neutra-800": "var(--clr-neutral-800)",
        "neutra-900": "var(--clr-neutral-900)",
        "surface-0": "rgb(var(--surface-0))",
        "surface-50": "rgb(var(--surface-50))",
        "surface-100": "rgb(var(--surface-100))",
        "surface-200": "rgb(var(--surface-200))",
        "surface-300": "rgb(var(--surface-300))",
        "surface-400": "rgb(var(--surface-400))",
        "surface-500": "rgb(var(--surface-500))",
        "surface-600": "rgb(var(--surface-600))",
        "surface-700": "rgb(var(--surface-700))",
        "surface-800": "rgb(var(--surface-800))",
        "surface-900": "rgb(var(--surface-900))",
        "surface-950": "rgb(var(--surface-950))",
        "bg-color-100": "#FBF6EF",
        "border-color-100": "#E0D9D1",
      },
      flex: {
        10: "1 0 auto",
      },
    },
  },
  plugins: [
    // Uncomment to use the custom variant
    // function ({ addVariant }) {
    //   addVariant("not-last", "&:not(:last-child)");
    // },
  ],
};
