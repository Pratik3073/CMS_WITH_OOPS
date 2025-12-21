/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./includes/*.php",
    "./src/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        'cms-blue': '#1A446C',
        'cms-light-blue': '#689DC1',
        'cms-very-light-blue': '#D4E6F4',
        'cms-light-tan': '#EEE4B9',
        'cms-burgundy': '#8D0D19'
      }
    },
  },
  plugins: [],
}
