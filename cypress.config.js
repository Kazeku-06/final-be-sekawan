import { defineConfig } from "cypress";
import FormData from "form-data";
import https from "https";
import http from "http";

/**
 * Task: uploadPelangganData
 * Kirim multipart/form-data dari Node.js (bukan browser),
 * karena cy.request() tidak support FormData browser untuk file upload.
 */
function uploadPelangganData({ baseUrl, token, pelangganId, jenis }) {
  return new Promise((resolve, reject) => {
    // PNG 1×1 pixel minimal — valid image file
    const pngBuffer = Buffer.from(
      "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==",
      "base64"
    );

    const form = new FormData();
    form.append("pelanggan_data_pelanggan_id", String(pelangganId));
    form.append("pelanggan_data_jenis", jenis);
    form.append("pelanggan_data_file", pngBuffer, {
      filename: "ktp.png",
      contentType: "image/png",
    });

    const url = new URL(`${baseUrl}/api/pelanggan-data`);
    const isHttps = url.protocol === "https:";
    const lib = isHttps ? https : http;

    const options = {
      hostname: url.hostname,
      port: url.port || (isHttps ? 443 : 80),
      path: url.pathname,
      method: "POST",
      headers: {
        ...form.getHeaders(),
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    };

    const req = lib.request(options, (res) => {
      let data = "";
      res.on("data", (chunk) => (data += chunk));
      res.on("end", () => {
        try {
          resolve({ status: res.statusCode, body: JSON.parse(data) });
        } catch {
          resolve({ status: res.statusCode, body: data });
        }
      });
    });

    req.on("error", reject);
    form.pipe(req);
  });
}

export default defineConfig({
  e2e: {
    baseUrl: "http://localhost:8000",
    setupNodeEvents(on) {
      on("task", {
        uploadPelangganData,
      });
    },
  },
});
