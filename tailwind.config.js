/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.php",
    "./public/**/*.php",
    "./app/Core/Router.php"
  ],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        primary: "#007281",
        secondary: "#E41E26",
        "slate-custom": "#111827",
      },
      fontFamily: {
        sans: ["'Plus Jakarta Sans'", "sans-serif"],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
