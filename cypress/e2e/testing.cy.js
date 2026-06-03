/**
 * E2E API Testing — CV. Amanah Elektronik
 * Base URL: http://localhost:8000
 *
 * Jalankan server dulu: php artisan serve
 * Lalu: npx cypress open  (atau npx cypress run)
 */

const BASE = "/api";

// ─── Helper: JSON headers ─────────────────────────────────────────────────────
const headers = (token = null) => {
  const h = { "Content-Type": "application/json", Accept: "application/json" };
  if (token) h["Authorization"] = `Bearer ${token}`;
  return h;
};

// ─── Shared state — diisi bertahap antar suite ────────────────────────────────
let token        = null;
let kategoriId   = null;
let alatId       = null;
let pelangganId  = null;
let pelangganDataId = null;
let penyewaanId  = null;

// ─── Helper: login dan simpan token ──────────────────────────────────────────
function doLogin() {
  cy.request({
    method: "POST",
    url: `${BASE}/auth/login`,
    headers: headers(),
    body: { admin_username: "admin", admin_password: "password123" },
  }).then((res) => {
    token = res.body.data.access_token;
  });
}

// =============================================================================
// SUITE 1 — Authentication
// =============================================================================
describe("1. Authentication", () => {
  it("POST /auth/login — gagal validasi (body kosong)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/auth/login`,
      headers: headers(),
      body: {},
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.success).to.be.false;
      expect(res.body.message).to.eq("Validation Error");
      expect(res.body.errors).to.have.property("admin_username");
      expect(res.body.errors).to.have.property("admin_password");
    });
  });

  it("POST /auth/login — gagal (password salah)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/auth/login`,
      headers: headers(),
      body: { admin_username: "admin", admin_password: "salah123" },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(401);
      expect(res.body.success).to.be.false;
    });
  });

  it("POST /auth/login — berhasil dan dapat token", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/auth/login`,
      headers: headers(),
      body: { admin_username: "admin", admin_password: "password123" },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
      expect(res.body.data).to.have.property("access_token");
      expect(res.body.data.token_type).to.eq("bearer");
      expect(res.body.data.expires_in).to.be.a("number");
      token = res.body.data.access_token;
    });
  });

  it("GET /auth/me — berhasil dengan token", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/auth/me`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
      expect(res.body.data).to.have.property("admin_id");
      expect(res.body.data.admin_username).to.eq("admin");
    });
  });
});

// =============================================================================
// SUITE 2 — Unauthorized Access
// =============================================================================
describe("2. Unauthorized Access", () => {
  it("GET /kategori — tanpa token → 401", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/kategori`,
      headers: { Accept: "application/json" },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(401);
      expect(res.body.success).to.be.false;
    });
  });

  it("GET /alat — token palsu → 401", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/alat`,
      headers: headers("token.palsu.tidak.valid"),
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(401);
      expect(res.body.success).to.be.false;
    });
  });
});

// =============================================================================
// SUITE 3 — CRUD Kategori
// =============================================================================
describe("3. CRUD Kategori", () => {
  before(() => { doLogin(); });

  it("POST /kategori — validasi error (body kosong)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/kategori`,
      headers: headers(token),
      body: {},
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.success).to.be.false;
      expect(res.body.errors).to.have.property("kategori_name");
    });
  });

  it("POST /kategori — berhasil buat kategori", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/kategori`,
      headers: headers(token),
      body: { kategori_name: "Laptop Test" },
    }).then((res) => {
      expect(res.status).to.eq(201);
      expect(res.body.success).to.be.true;
      expect(res.body.data.kategori_name).to.eq("Laptop Test");
      kategoriId = res.body.data.kategori_id;
    });
  });

  it("GET /kategori — tampilkan semua kategori", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/kategori`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
      expect(res.body.data).to.be.an("array");
      expect(res.body.data.length).to.be.greaterThan(0);
    });
  });

  it("GET /kategori/:id — detail kategori", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/kategori/${kategoriId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.kategori_id).to.eq(kategoriId);
    });
  });

  it("PATCH /kategori/:id — update nama", () => {
    cy.request({
      method: "PATCH",
      url: `${BASE}/kategori/${kategoriId}`,
      headers: headers(token),
      body: { kategori_name: "Laptop Updated" },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.kategori_name).to.eq("Laptop Updated");
    });
  });

  it("GET /kategori/9999 — tidak ditemukan → 404", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/kategori/9999`,
      headers: headers(token),
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(404);
      expect(res.body.success).to.be.false;
    });
  });
});

// =============================================================================
// SUITE 4 — CRUD Alat
// =============================================================================
describe("4. CRUD Alat", () => {
  it("POST /alat — validasi error (field kosong)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/alat`,
      headers: headers(token),
      body: {},
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.errors).to.have.property("alat_nama");
    });
  });

  it("POST /alat — validasi error (kategori tidak ada)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/alat`,
      headers: headers(token),
      body: {
        alat_kategori_id: 9999,
        alat_nama: "Test",
        alat_deskripsi: "desc",
        alat_hargaperhari: 50000,
        alat_stok: 5,
      },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.errors).to.have.property("alat_kategori_id");
    });
  });

  it("POST /alat — berhasil buat alat", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/alat`,
      headers: headers(token),
      body: {
        alat_kategori_id: kategoriId,
        alat_nama: "Lenovo ThinkPad Cypress",
        alat_deskripsi: "Laptop untuk testing",
        alat_hargaperhari: 100000,
        alat_stok: 10,
      },
    }).then((res) => {
      expect(res.status).to.eq(201);
      expect(res.body.success).to.be.true;
      expect(res.body.data.alat_stok).to.eq(10);
      expect(res.body.data.kategori).to.exist;
      alatId = res.body.data.alat_id;
    });
  });

  it("GET /alat — tampilkan semua dengan relasi kategori", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/alat`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data).to.be.an("array");
      expect(res.body.data[0]).to.have.property("kategori");
    });
  });

  it("GET /alat/:id — detail alat", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/alat/${alatId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.alat_id).to.eq(alatId);
    });
  });

  it("PATCH /alat/:id — update stok menjadi 20", () => {
    cy.request({
      method: "PATCH",
      url: `${BASE}/alat/${alatId}`,
      headers: headers(token),
      body: { alat_stok: 20 },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.alat_stok).to.eq(20);
    });
  });
});

// =============================================================================
// SUITE 5 — CRUD Pelanggan
// =============================================================================
describe("5. CRUD Pelanggan", () => {
  it("POST /pelanggan — validasi error (email tidak valid)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/pelanggan`,
      headers: headers(token),
      body: {
        pelanggan_nama: "Test",
        pelanggan_alamat: "Jl. Test",
        pelanggan_notelp: "08123",
        pelanggan_email: "bukan-email",
      },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.errors).to.have.property("pelanggan_email");
    });
  });

  it("POST /pelanggan — berhasil buat pelanggan", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/pelanggan`,
      headers: headers(token),
      body: {
        pelanggan_nama: "Budi Cypress",
        pelanggan_alamat: "Jl. Cypress No.1 Surabaya",
        pelanggan_notelp: "081234567890",
        // timestamp agar email unik setiap run
        pelanggan_email: `budi.cypress.${Date.now()}@test.com`,
      },
    }).then((res) => {
      expect(res.status).to.eq(201);
      expect(res.body.success).to.be.true;
      pelangganId = res.body.data.pelanggan_id;
    });
  });

  it("GET /pelanggan — tampilkan semua", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/pelanggan`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data).to.be.an("array");
    });
  });

  it("GET /pelanggan/:id — detail pelanggan", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/pelanggan/${pelangganId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.pelanggan_id).to.eq(pelangganId);
    });
  });

  it("PATCH /pelanggan/:id — update alamat", () => {
    cy.request({
      method: "PATCH",
      url: `${BASE}/pelanggan/${pelangganId}`,
      headers: headers(token),
      body: { pelanggan_alamat: "Jl. Update No.99 Malang" },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.pelanggan_alamat).to.eq("Jl. Update No.99 Malang");
    });
  });
});

// =============================================================================
// SUITE 6 — Upload Identitas Pelanggan
// Menggunakan cy.task() karena cy.request() tidak support multipart FormData
// Task didefinisikan di cypress.config.js menggunakan Node http + form-data
// =============================================================================
describe("6. Upload Identitas Pelanggan", () => {
  it("POST /pelanggan-data — validasi error (tanpa file)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/pelanggan-data`,
      headers: headers(token),
      // kirim JSON biasa — field file tidak ada → 422
      body: {
        pelanggan_data_pelanggan_id: pelangganId,
        pelanggan_data_jenis: "KTP",
      },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.errors).to.have.property("pelanggan_data_file");
    });
  });

  it("POST /pelanggan-data — validasi error (jenis tidak valid)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/pelanggan-data`,
      headers: headers(token),
      body: {
        pelanggan_data_pelanggan_id: pelangganId,
        pelanggan_data_jenis: "PASSPORT",
      },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.errors).to.have.property("pelanggan_data_jenis");
    });
  });

  it("POST /pelanggan-data — upload KTP berhasil (via cy.task)", () => {
    // cy.task() jalan di Node.js → bisa pakai form-data package untuk multipart
    cy.task("uploadPelangganData", {
      baseUrl: "http://localhost:8000",
      token: token,
      pelangganId: pelangganId,
      jenis: "KTP",
    }).then((res) => {
      expect(res.status).to.eq(201);
      expect(res.body.success).to.be.true;
      expect(res.body.data.pelanggan_data_jenis).to.eq("KTP");
      expect(res.body.data.pelanggan_data_file).to.include("identitas");
      // Simpan ID untuk test GET dan DELETE berikutnya
      pelangganDataId = res.body.data.pelanggan_data_id;
    });
  });

  it("GET /pelanggan-data/:id — detail identitas", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/pelanggan-data/${pelangganDataId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
      expect(res.body.data).to.have.property("pelanggan");
      expect(res.body.data.pelanggan_data_id).to.eq(pelangganDataId);
    });
  });

  it("GET /pelanggan-data — tampilkan semua identitas", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/pelanggan-data`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data).to.be.an("array");
    });
  });
});

// =============================================================================
// SUITE 7 — Transaksi Penyewaan
// =============================================================================
describe("7. Transaksi Penyewaan", () => {
  it("POST /penyewaan — validasi error (tanpa details)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/penyewaan`,
      headers: headers(token),
      body: {
        penyewaan_pelanggan_id: pelangganId,
        penyewaan_tglsewa: "2026-07-01",
        penyewaan_tglkembali: "2026-07-05",
        // details sengaja tidak dikirim
      },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.errors).to.have.property("details");
    });
  });

  it("POST /penyewaan — validasi error (tglkembali sebelum tglsewa)", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/penyewaan`,
      headers: headers(token),
      body: {
        penyewaan_pelanggan_id: pelangganId,
        penyewaan_tglsewa: "2026-07-10",
        penyewaan_tglkembali: "2026-07-05",
        details: [{ alat_id: alatId, jumlah: 1 }],
      },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.errors).to.have.property("penyewaan_tglkembali");
    });
  });

  it("POST /penyewaan — berhasil buat transaksi + hitung total + kurangi stok", () => {
    // stok saat ini: 20 (di-update di suite 4)
    // durasi: 1 Jul → 6 Jul = 5 hari
    // subharga: 100.000 × 2 × 5 = 1.000.000
    cy.request({
      method: "POST",
      url: `${BASE}/penyewaan`,
      headers: headers(token),
      body: {
        penyewaan_pelanggan_id: pelangganId,
        penyewaan_tglsewa: "2026-07-01",
        penyewaan_tglkembali: "2026-07-06",
        penyewaan_sttspembayaran: "DP",
        details: [{ alat_id: alatId, jumlah: 2 }],
      },
    }).then((res) => {
      expect(res.status).to.eq(201);
      expect(res.body.success).to.be.true;
      // Total: 100.000 × 2 × 5 hari = 1.000.000
      expect(res.body.data.penyewaan_totalharga).to.eq(1000000);
      // Default status
      expect(res.body.data.penyewaan_sttskembali).to.eq("Belum Kembali");
      expect(res.body.data.penyewaan_sttspembayaran).to.eq("DP");
      // Detail tersimpan
      expect(res.body.data.detail).to.have.length(1);
      expect(res.body.data.detail[0].penyewaan_detail_subharga).to.eq(1000000);
      // Stok berkurang: 20 − 2 = 18
      expect(res.body.data.detail[0].alat.alat_stok).to.eq(18);
      penyewaanId = res.body.data.penyewaan_id;
    });
  });

  it("POST /penyewaan — stok tidak cukup → 422", () => {
    // Minta 999 unit, sisa stok 18
    cy.request({
      method: "POST",
      url: `${BASE}/penyewaan`,
      headers: headers(token),
      body: {
        penyewaan_pelanggan_id: pelangganId,
        penyewaan_tglsewa: "2026-08-01",
        penyewaan_tglkembali: "2026-08-03",
        details: [{ alat_id: alatId, jumlah: 999 }],
      },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body.success).to.be.false;
      expect(res.body.message).to.include("tidak mencukupi");
    });
  });

  it("GET /penyewaan — tampilkan semua transaksi", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/penyewaan`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data).to.be.an("array");
    });
  });

  it("GET /penyewaan/:id — detail dengan relasi pelanggan & alat", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/penyewaan/${penyewaanId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data).to.have.property("pelanggan");
      expect(res.body.data).to.have.property("detail");
      expect(res.body.data.detail[0]).to.have.property("alat");
    });
  });

  it("PATCH /penyewaan/:id — update status pembayaran & kembali", () => {
    cy.request({
      method: "PATCH",
      url: `${BASE}/penyewaan/${penyewaanId}`,
      headers: headers(token),
      body: {
        penyewaan_sttspembayaran: "Lunas",
        penyewaan_sttskembali: "Sudah Kembali",
      },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.penyewaan_sttspembayaran).to.eq("Lunas");
      expect(res.body.data.penyewaan_sttskembali).to.eq("Sudah Kembali");
    });
  });
});

// =============================================================================
// SUITE 8 — Penyewaan Detail (read-only)
// =============================================================================
describe("8. Penyewaan Detail", () => {
  it("GET /penyewaan-detail — tampilkan semua", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/penyewaan-detail`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
      expect(res.body.data).to.be.an("array");
    });
  });

  it("GET /penyewaan-detail/:id — detail satu item", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/penyewaan-detail/${penyewaanId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data).to.have.property("alat");
      expect(res.body.data).to.have.property("penyewaan");
    });
  });

  it("GET /penyewaan-detail/9999 — tidak ditemukan → 404", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/penyewaan-detail/9999`,
      headers: headers(token),
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(404);
      expect(res.body.success).to.be.false;
    });
  });
});

// =============================================================================
// SUITE 9 — Format Response JSON
// =============================================================================
describe("9. Format Response JSON", () => {
  it("Success response punya struktur { success, message, data }", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/kategori`,
      headers: headers(token),
    }).then((res) => {
      expect(res.body).to.have.all.keys("success", "message", "data");
      expect(res.body.success).to.be.true;
      expect(res.body.message).to.be.a("string");
    });
  });

  it("Error response punya struktur { success, message, data, errors }", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/kategori`,
      headers: headers(token),
      body: {},
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.body).to.have.all.keys("success", "message", "data", "errors");
      expect(res.body.success).to.be.false;
    });
  });

  it("Create resource → HTTP 201", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/kategori`,
      headers: headers(token),
      body: { kategori_name: "Kategori Response Test" },
    }).then((res) => {
      expect(res.status).to.eq(201);
    });
  });

  it("Get resource → HTTP 200", () => {
    cy.request({
      method: "GET",
      url: `${BASE}/kategori`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });
});

// =============================================================================
// SUITE 10 — Cleanup (hapus semua data test, urutan penting)
// =============================================================================
describe("10. Cleanup", () => {
  it("DELETE /pelanggan-data/:id — hapus identitas", () => {
    cy.request({
      method: "DELETE",
      url: `${BASE}/pelanggan-data/${pelangganDataId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
    });
  });

  it("DELETE /penyewaan/:id — hapus transaksi", () => {
    cy.request({
      method: "DELETE",
      url: `${BASE}/penyewaan/${penyewaanId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
    });
  });

  it("DELETE /alat/:id — hapus alat", () => {
    cy.request({
      method: "DELETE",
      url: `${BASE}/alat/${alatId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
    });
  });

  it("DELETE /pelanggan/:id — hapus pelanggan", () => {
    cy.request({
      method: "DELETE",
      url: `${BASE}/pelanggan/${pelangganId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
    });
  });

  it("DELETE /kategori/:id — hapus kategori", () => {
    cy.request({
      method: "DELETE",
      url: `${BASE}/kategori/${kategoriId}`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
    });
  });

  it("POST /auth/logout — invalidate token", () => {
    cy.request({
      method: "POST",
      url: `${BASE}/auth/logout`,
      headers: headers(token),
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.success).to.be.true;
    });
  });
});
