# 📚 Dokumentasi — IPB Reserve (Ruang Rapat SSMI)

## Daftar Isi

### Untuk Developer Baru
| Dokumen | Deskripsi |
|---------|-----------|
| [Setup & Instalasi](installation-manual/setup.md) | Cara menjalankan project dari nol (Docker) |
| [Arsitektur Sistem](architecture.md) | System overview, tech stack, folder structure |
| [Database Schema](tech-docs/database.md) | Semua tabel, kolom, relasi, dan penjelasannya |
| [User Flows](tech-docs/user-flows.md) | Alur lengkap setiap proses (booking, approve, cancel, dll) |

### Untuk Maintenance & Upgrade
| Dokumen | Deskripsi |
|---------|-----------|
| [Edge Cases & Business Logic](tech-docs/edge-cases.md) | Semua edge case yang ditangani dan cara kerjanya |
| [Booking Rules (Configurable)](tech-docs/booking-rules.md) | Aturan admin-configurable dan default values |
| [Cron Jobs](tech-docs/cron-jobs.md) | Scheduled tasks dan cara mengaktifkannya |
| [Developer Guide](tech-docs/developer-guide.md) | Konvensi, cara tambah fitur baru, common commands |
| [Changelog](releases/CHANGELOG.md) | Semua perubahan yang sudah dilakukan |

### Diagram (PlantUML)
| File | Deskripsi |
|------|-----------|
| [Architecture Diagram](tech-docs/diagram/architecture.puml) | Docker development setup |
| [ERD](tech-docs/diagram/erd.puml) | Entity-Relationship Diagram |
| [Use Case Diagram](tech-docs/diagram/use-case.puml) | Semua aktor dan use case |
| [Sequence: Booking](tech-docs/diagram/sequence-booking.puml) | Alur booking dari user submit sampai saved |
| [Sequence: Admin Approve](tech-docs/diagram/sequence-admin-approve.puml) | Alur admin review reservasi |

To render `.puml` files to PNG, use the VS Code PlantUML extension (Alt+D to preview) or run `python render.py` in the diagram folder.

### Dokumen Standar (Akademik)
| Folder | Isi |
|--------|-----|
| `standard-docs/01_DKB_BRD/` | Business Requirements Document |
| `standard-docs/03_SKPL_SRS/` | Software Requirements Specification |

---

## Quick Links

- **Frontend URL:** http://localhost:20080
- **Backend URL:** http://localhost:21080
- **GitHub:** https://github.com/Jeff146354/capstone-ssmi-ruang-rapat
- **Tech Stack:** Yii2 PHP + MySQL + Docker
- **Design System:** Plus Jakarta Sans, primary color `#FF6B00`

---

## What's Not Implemented Yet

- Production deployment (VPS/server)
- QR Check-in (UI only, no real QR generation)
- Email verification (code ready, needs SMTP server configuration)
- Cron jobs (commands exist but not scheduled on any server)
- Damage reporting feature (from original use case diagram)
