/**
 * Asset pipelines — CSS (sass → concat → clean-css) and JS (concat → terser).
 * Output paths and file names mirror the old Grunt pipeline exactly
 * (the PHP code enqueues these files — never change them).
 */

import { dirname, join, resolve } from "node:path";
import { existsSync, readdirSync } from "node:fs";
import { log } from "./consoleHelpers.mjs";
import { config } from "./config.mjs";
import {
  compileSCSSEntries,
  concatFiles,
  minifyCSS,
  minifyJS,
  writeFileAtomic,
  prettierSources,
  cleanGlob,
} from "./tools.mjs";

const root = resolve(dirname(new URL(import.meta.url).pathname), "..");

/**
 * Banner prepended to concatenated bundles (grunt `concat` template).
 *
 * @return {string}
 */
function concatBanner() {
  return [
    `/*! ${config.title} - ${config.version}`,
    ` * ${config.website || config.homepage}`,
    ` * Copyright (c) ${config.buildYear}`,
    ` * Licensed ${config.license} */`,
    "",
  ].join("\n");
}

/**
 * Banner prepended to minified bundles (grunt `uglify` template — note the `;`).
 *
 * @return {string}
 */
function minBanner() {
  return [
    `/*! ${config.title} - ${config.version}`,
    ` * ${config.website || config.homepage}`,
    ` * Copyright (c) ${config.buildYear};`,
    ` * Licensed ${config.license} */`,
    "",
  ].join("\n");
}

/**
 * Strip sourceMappingURL comments (build mode — no maps are written).
 *
 * @param {string} css CSS content.
 * @return {string}
 */
function stripMapComments(css) {
  return css.replace(/\/\*# sourceMappingURL=[^*]*\*\/\n?/g, "");
}

/**
 * List files in a directory (no recursion), sorted, filtered by extension.
 *
 * @param {string}   dir Absolute directory.
 * @param {string[]} extensions Allowed extensions (with dot).
 * @return {string[]} Absolute file paths.
 */
function listFiles(dir, extensions) {
  if (!existsSync(dir)) {
    return [];
  }
  return readdirSync(dir, { withFileTypes: true })
    .filter((e) => e.isFile() && extensions.includes(ext(e.name)))
    .map((e) => join(dir, e.name))
    .sort();
}

function ext(name) {
  const idx = name.lastIndexOf(".");
  return idx === -1 ? "" : name.slice(idx);
}

/**
 * CSS pipeline: compile SCSS entries → concat admin/frontend → minify.
 * Outputs (grunt parity): assets/styles/fleet-admin.css and fleet.css,
 * plus .min.css versions.
 *
 * @param {object} options
 * @param {boolean} options.maps   Write source maps (dev mode).
 * @param {boolean} options.strip  Strip sourceMappingURL comments.
 */
export async function buildCSS({ maps = false, strip = false } = {}) {
  const compiled = await compileSCSSEntries(maps);

  // Admin: concat everything under assets/styles/admin/.
  const adminCssDir = join(root, config.stylesDir, "admin");
  const adminFiles = listFiles(adminCssDir, [".css"]);
  if (adminFiles.length) {
    let adminCss = await concatFiles(adminFiles, concatBanner());
    if (strip) {
      adminCss = stripMapComments(adminCss);
    }
    const adminOut = join(root, config.stylesDir, "fleet-admin.css");
    writeFileAtomic(adminOut, adminCss);
    const min = minifyCSS(adminCss);
    writeFileAtomic(adminOut.replace(/\.css$/, ".min.css"), minBanner() + min);
  } else {
    log("css — assets/styles/admin/ has no sources, admin bundle skipped", "w");
  }

  // Frontend: settings.css first (when present), then the rest of assets/styles/frontend/.
  const frontendDir = join(root, config.stylesDir, "frontend");
  const all = listFiles(frontendDir, [".css"]);
  if (all.length) {
    const settings = all.filter((f) => f.endsWith("settings.css"));
    const rest = all.filter((f) => !f.endsWith("settings.css"));
    let frontendCss = await concatFiles([...settings, ...rest], concatBanner());
    if (strip) {
      frontendCss = stripMapComments(frontendCss);
    }
    const frontendOut = join(root, config.stylesDir, "fleet.css");
    writeFileAtomic(frontendOut, frontendCss);
    const min = minifyCSS(frontendCss);
    writeFileAtomic(
      frontendOut.replace(/\.css$/, ".min.css"),
      minBanner() + min,
    );
  } else {
    log("css — assets/styles/frontend/ is empty, frontend bundle skipped", "w");
  }
  log(
    `css — ${compiled.length} SCSS entries processed${adminFiles.length ? " → admin bundle" : " (admin skipped)"}${all.length ? " → frontend bundle" : " (frontend skipped)"}`,
    "s",
  );
}

/**
 * JS pipeline: concat admin sources → fleet.js → terser minify → fleet.min.js.
 */
export async function buildJS() {
  const srcDir = join(root, config.jsSrcDir);
  const files = listFiles(srcDir, [".js"]);
  if (!files.length) {
    log(`js — ${config.jsSrcDir}/ has no sources, bundle skipped`, "w");
    return;
  }
  const concat = await concatFiles(files, concatBanner());
  const outFile = join(root, config.scriptsDir, config.jsBundle);
  writeFileAtomic(outFile, concat);
  const min = await minifyJS(concat, minBanner());
  writeFileAtomic(outFile.replace(/\.js$/, ".min.js"), min);
  log("js — bundles concatenated and minified", "s");
}

/**
 * Prettier pass over the JS/JSON sources (build pipelines run it first).
 */
export async function formatSources() {
  await prettierSources([
    "assets/scripts/admin/src/**/*.js",
    "bundler/**/*.js",
    "bundler/**/*.mjs",
    "*.js",
  ]);
  log("prettier — sources formatted", "s");
}

/**
 * Remove built files (grunt `clean` semantics).
 */
export async function cleanCompiled() {
  await cleanGlob("assets/scripts/admin/*.js");
  await cleanGlob("assets/scripts/admin/*.map");
  await cleanGlob("assets/styles/*.css");
  await cleanGlob("assets/styles/**/*.css");
  await cleanGlob("assets/styles/**/*.map");
  await cleanGlob("languages/*.mo");
  log("clean — built JS/CSS and languages/*.mo removed", "i");
}
