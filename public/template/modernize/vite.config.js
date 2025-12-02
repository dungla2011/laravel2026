import { defineConfig } from "vite";

import fs from "fs-extra";
import path from "path";

import rollup from 'rollup-plugin-copy';

const folder = {
    src: "src/", // source files
    src_assets: "src/assets/", // source assets files
    dist: "", // build files
    dist_assets: "assets/", //build assets files
};

export default defineConfig({
    plugins: [],
    base: "",
    // logLevel: 'error', // if you want to disable logging use 'info' | 'warn' | 'error' | 'silent'
    clearScreen: true,
    root: path.resolve(__dirname, folder.src),
    build: {
        outDir: path.resolve(__dirname,  folder.dist),
        emptyOutDir: false,
        // watch: {},  // if you want to watch your build files
        rollupOptions: {
            manualChunks: undefined,
            input: {
                styles: folder.src_assets + "scss/styles.scss",
            },
            output: {
                assetFileNames: (css) => {
                    const ext = css.name.split(".").pop();
                    if (ext == "css") {
                        return "assets/css/" + `[name]` + ".css";
                    } else if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(ext)) {
                        return "assets/images/" + css.name;
                    } else {
                        return "assets/css/" + css.name;
                    }
                },
                inlineDynamicImports: false,
                format: "cjs",
                entryFileNames: "assets/js/" + `[name]` + `.js`,
            },
            external: [
                // Add any other external dependencies here
                /^assets\/libs\//, // This regex matches the external import path
            ],
            plugins: [
                // ...other plugins
                rollup({
                    targets: [
                        { src: folder.src_assets + "css", dest: folder.dist_assets },
                        { src: folder.src_assets + "images", dest: folder.dist_assets },
                        { src: folder.src_assets + "js", dest: folder.dist_assets },
                    ],
                }),
                {
                    name: "copy-specific-packages",
                    async writeBundle() {
                        const outputPath = path.resolve(__dirname, folder.dist_assets); // Adjust the destination path
                        const outputPathSrc = path.resolve(__dirname, folder.dist_assets); // Adjust the destination path
                        const configPath = path.resolve(__dirname, "package-libs-config.json");

                        try {
                            const configContent = await fs.readFile(configPath, "utf-8");
                            const { packagesToCopy } = JSON.parse(configContent);

                            for (const packageName of packagesToCopy) {
                                let isDist = fs.existsSync(
                                    path.join(__dirname, "node_modules", packageName + "/dist")
                                )
                                const destPackagePath = path.join(outputPath, "libs", packageName, isDist ? "/dist" : "");
                                const destPackagePathSrc = path.join(outputPathSrc, "libs", packageName, isDist ? "/dist" : "");

                                const sourcePath = fs.existsSync(path.join(__dirname, "node_modules", packageName + "/dist"))
                                    ? path.join(__dirname, "node_modules", packageName + "/dist")
                                    : path.join(__dirname, "node_modules", packageName);

                                try {
                                    await fs.access(sourcePath, fs.constants.F_OK);
                                    await fs.copy(sourcePath, destPackagePath);
                                    await fs.copy(sourcePath, destPackagePathSrc);
                                } catch (error) {
                                    console.error(`Package ${packageName} does not exist.`);
                                }
                            }
                        } catch (error) {
                            console.error("Error copying and renaming packages:", error);
                        }
                    },
                },

            ],
        },
    }
});
