const colors = require('tailwindcss/colors')
module.exports = {
  mode: 'jit',
  purge: [
    './src/styles.css',
    '../templates/**/*.twig'
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      fontSize: {
        '10xl': '10rem',
        '11xl': '12rem',
        '12xl': '14rem',
        '13xl': '16rem',
      }
    },
    fontFamily: {
      phosphate: ['Phosphate'],
      'phosphate-inline': ['PhosphateInline'],
      arsilon: ['Arsilon'],
    },
    colors: {
      green: 'rgb(101, 253, 48)',
      blue: 'rgb(38, 218, 253)',
      yellow: {
        DEFAULT: 'rgb(252, 193, 45)',
        light: 'rgb(254, 218, 111)',
      },
      pink: {
        DEFAULT: 'rgb(252, 23, 133)',
        light: 'rgb(254, 184, 232)',
      },
      orange: {
        DEFAULT: 'rgb(252, 86, 30)',
        light: 'rgb(253, 176, 108)',
      },
      brown: 'rgb(73, 18, 18)',
      white: colors.white,
      black: colors.black,
      gray: colors.gray,
      warmGray: colors.warmGray
    }
  },
  variants: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
