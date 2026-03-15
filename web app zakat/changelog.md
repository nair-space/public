# zakat app v 0.1.0:

> adding various function to zakat fitrah page

## adding function to zakat fitrah page

-add function to put the data into the respectable database once user click simpan

-give data berhasil disimpan message if the data is sucessfully put into the database

-for riwayat zakat fitrah, shows proper data in the database that user already input it in, and give edit button to edit the data if the user have some error in the process

-the id nota input should be unique, so when use click simpan and after 300 ms the user put id nota input, the form will check the database if there is a duplication in the database or not. it will check all existingZakatFitrah, existingZakatMaal, existingFidyah, existingInfaq, existingShodaqoh, existingPengeluaran table for id nota

# zakat app v 0.1.1:

> adding zakat fitrah function to zakat maal, fidyah, infaq and shodaqoh

## adding function to zakat maal, fidyah, infaq and shodaqoh page

-add function to put the data into the respectable database once user click simpan

-give data berhasil disimpan message if the data is sucessfully put into the database

-for riwayat, shows proper data in the database that user already input it in, and give edit button to edit the data if the user have some error in the process

-the id nota input should be unique, so when use click simpan and after 300 ms the user put id nota input, the form will check the database if there is a duplication in the database or not. it will check all existingZakatFitrah, existingZakatMaal, existingFidyah, existingInfaq, existingShodaqoh, existingPengeluaran table for id nota

# zakat app v 0.1.2:

> login account management

## Account revocation and make it simpler

-remove 'daftar akun' function from login page.

-remove 'lupa password' function from login page.

-remove 'email form' function from login page, I will make the login page only using ID and password.

-remove all account that already exist on the database

-after removing, make this one administrator password and push it into the database so I can login using it to check the application
id:
admin@ikhlash
password:
!123qweasd

---

# zakat app v 0.1.3:

> adding administrator account creation page for amil accounts with privilege management

## Admin account creation page - 'tambah akun amil':

-add 'tambah akun amil' page only accessible by administrator account

-this page contains form for nama amil, ID login, and password

-the ID must be unique across all users (both admin and amil accounts), with real-time duplicate check after 300ms delay

-success message 'Akun amil berhasil dibuat!' displayed after successful creation

## Amil account management features:

- **Amil account list**: Display all existing amil accounts below the creation form with nama, login ID, and management buttons

- **Password change functionality**: Admin can reset amil passwords using '🔐 Ganti' button with input field for new password

- **Account deletion**: Admin can delete amil accounts using '🗑️ Hapus' button with confirmation dialog

- **Deletion confirmation**: Modal dialog asking "yakin untuk hapus akun?" with account name display, "Tidak" cancel and "Ya" confirm buttons

## Technical improvements:

- **Password hashing**: Migrated from scrypt to bcrypt for consistent hashing across all accounts

- **Database direct insertion**: Amil accounts created via direct DB insertion to prevent session switching

- **Real-time validation**: ID uniqueness check with 300ms debounce for smooth user experience

- **Admin session persistence**: Admin remains logged in after creating/resetting/deleting amil accounts

- **Component enhancements**: Added 'danger' variant and 'size' props to GlassButton and GlassInput components

- **Type safety**: All components properly typed with no TypeScript errors (svelte-check clean)

---

# zakat app v 0.1.6:

> adding function for lihat laporan page

## Lihat laporan totals and net balance:

- added server-side data loading for laporan page to read all transaksi from `zakat_fitrah`, `zakat_maal`, `fidyah`, `infaq`, `shodaqoh`, and `pengeluaran` tables

- implemented total income calculation for:
  - zakat fitrah uang
  - zakat fitrah beras
  - zakat maal
  - fidyah uang
  - fidyah beras
  - infaq
  - shodaqoh

- implemented total outcome calculation from `pengeluaran` for:
  - zakat fitrah uang outcome
  - zakat fitrah beras outcome
  - zakat maal uang outcome
  - fidyah uang outcome
  - fidyah beras outcome
  - infaq uang outcome
  - shodaqoh uang outcome

- implemented net balance per category with formula:
  - `net balance = total income - total outcome`

- updated `lihat-laporan` table to display real database totals (no placeholder values)

---

# zakat app v 0.1.7:

> adding function for import and export data and ringkasan pengeluaran

- added import and export functionality for all data types (zakat fitrah, zakat maal, fidyah, infaq, shodaqoh, pengeluaran)

- added ringkasan pengeluaran section to show total pengeluaran uang and beras
- added ringkasan pemasukan section to show total pemasukan in zakat fitrah, zakat maal, fidyah, infaq, and shodaqoh

---

# zakat app v 0.1.8:

> adding function for cetak laporan page

## Cetak laporan (PDF) features:

- added date-range filter and checklist filters to choose which laporan to include
- added server-side calculation for income/outcome totals within selected date range
- added PDF export with standardized template:
  - title, date range, income lines, outcome lines, and net balance lines
- added laporan preview section before export

---

# zakat app v 0.1.9-beta-1 :

> make the design responsive for phone

- added mobile sidebar drawer with overlay and close button
- added topbar hamburger menu for phone
- adjusted layout spacing for small screens

---

# zakat app v 0.1.10-beta-1 :

> release notes make it live on cloud

- added deployment checklist and environment variable guidance
- tightened server-side authorization checks for data actions
- added login rate limiting for brute-force protection
- verified svelte-check clean before production

---

# zakat app v 0.2.2-beta-1 :

> performance optimization phase 3

- added daily/monthly summary tables for faster laporan totals
- backfill summary tables on first use to keep totals accurate
- export now runs as background job with status polling and download when ready
- laporan totals now read from summary tables instead of full-table scans

---

# zakat app v 0.2.3-beta-1 :

> single-entry validation & speed insights

- enforce one entry per ID nota for zakat fitrah, fidyah, and pengeluaran inputs
- disable submit and highlight button when multiple entries are filled
- added Vercel Speed Insights for production performance monitoring

---
