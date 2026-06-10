# 📚 Dokumentasi — IPB Reserve (Ruang Rapat SSMI)

## Daftar Isi

### Untuk Developer Baru
| Dokumen | Deskripsi |
|---------|-----------|
| [Setup & Instalasi](installation-manual/setup.md) | Cara menjalankan project dari nol (Docker & lokal) |
| [Arsitektur Sistem](architecture.md) | High-level system overview, tech stack, folder structure |
| [Database Schema](tech-docs/database.md) | Semua tabel, kolom, relasi, dan penjelasannya |
| [User Flows](tech-docs/user-flows.md) | Alur lengkap setiap proses (booking, approve, cancel, dll) |

### Untuk Maintenance & Upgrade
| Dokumen | Deskripsi |
|---------|-----------|
| [Edge Cases & Business Logic](tech-docs/edge-cases.md) | Semua edge case yang ditangani dan cara kerjanya |
| [Booking Rules (Configurable)](tech-docs/booking-rules.md) | Aturan admin-configurable dan default values |
| [Cron Jobs](tech-docs/cron-jobs.md) | Scheduled tasks dan cara mengaktifkannya |

### Diagram (PlantUML)
| File | Deskripsi |
|------|-----------|
| [Architecture Diagram](tech-docs/diagram/architecture.puml) | Deployment & infrastructure |
| [ERD](tech-docs/diagram/erd.puml) | Entity-Relationship Diagram |
| [Use Case Diagram](tech-docs/diagram/use-case.puml) | Semua aktor dan use case |
| [Sequence: Booking](tech-docs/diagram/sequence-booking.puml) | Alur booking dari user submit sampai approved |
| [Sequence: Admin Approve](tech-docs/diagram/sequence-admin-approve.puml) | Alur admin review reservasi |

### Dokumen Standar (Akademik)
| Folder | Isi |
|--------|-----|
| `standard-docs/01_DKB_BRD/` | Business Requirements Document |
| `standard-docs/03_SKPL_SRS/` | Software Requirements Specification |

---

## Quick Links

- **Frontend URL**: `http://localhost:20080`
- **Backend URL**: `http://localhost:21080`
- **GitHub**: https://github.com/Jeff146354/capstone-ssmi-ruang-rapat
- **Tech Stack**: Yii2 PHP (Advanced Template) + MySQL + Docker + Bootstrap 5
- **Font**: Plus Jakarta Sans
- **Primary Color**: `#FF6B00`
