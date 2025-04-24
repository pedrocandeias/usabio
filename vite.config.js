import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import path from 'path';

export default defineConfig({
  root: '.',
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'assets/js/app.js'),
        styles: path.resolve(__dirname, 'assets/css/app.css'),
        print: path.resolve(__dirname, 'assets/css/print_projects.css'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        assetFileNames: 'css/[name][extname]'
      }
    }
  },
  plugins: [
    viteStaticCopy({
      targets: [
        {
          src: 'assets/img/*',
          dest: 'img'
        }
      ]
    })
  ]
});

