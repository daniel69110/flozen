/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        customBlue: '#293041',
      },
      fontFamily: {
        gwendolyn: ['Gwendolyn', 'sans-serif'],
      },
    },
  },
  plugins: [],
}

