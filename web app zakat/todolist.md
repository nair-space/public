# zakat app v 0.1.0:

> this v 0.1.0 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding various function to zakat fitrah page

## adding function to zakat fitrah page

-add function to put the data into the respectable database once user click simpan

-give data berhasil disimpan message if the data is sucessfully put into the database

-for riwayat zakat fitrah, shows proper data in the database that user already input it in, and give edit button to edit the data if the user have some error in the process

-the id nota input should be unique, so when use click simpan and after 300 ms the user put id nota input, the form will check the database if there is a duplication in the database or not. it will check all existingZakatFitrah, existingZakatMaal, existingFidyah, existingInfaq, existingShodaqoh, existingPengeluaran table for id nota

# for zakat app v 0.1.1:

> this v 0.1.1 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding zakat fitrah function to zakat maal, fidyah, infaq and shodaqoh

## The added zakat fitrah function are from zakat app v 0.1.0 are:

-add function to put the data into the respectable database once user click simpan

-give data berhasil disimpan message if the data is sucessfully put into the database

-for riwayat zakat fitrah, shows proper data in the database that user already input it in, and give edit button to edit the data if the user have some error in the process

-the id nota input should be unique, so when use click simpan and after 300 ms the user put id nota input, the form will check the database if there is a duplication in the database or not. it will check all existingZakatFitrah, existingZakatMaal, existingFidyah, existingInfaq, existingShodaqoh, existingPengeluaran table for id nota

## feature checked and it is all okay

## we already implemented these function in zakat maal, fidyah, infaq and shodaqoh page

---

# for zakat app v 0.1.2:

> this v 0.1.2 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> login account management

## Account revocation and make it simpler

-remove 'daftar akun' function from login page.

-remove 'lupa password' function from login page.

-remove 'email form' function from login page, I will make the login page only using ID and password.

-remove all account that already exist on the database

-after removing, make this one administrator password and push it into the database so I can login using it to check the application
id:

email:
(do not use email, just ID at the login page)
password:

---

# for zakat app v 0.1.3:

> this v 0.1.3 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding function for administrator account can make another account with previlege less than administrator (let's call this account 'amil', this amil account cannot make another amil account)

## Admin account creation page - 'tambah akun amil':

-add one page only for administrator account to create another amil account, this page is called 'tambah akun amil'.

-this page contain the name of the amil, ID to login, and password to login.

-the ID and password should be unique, so when use click simpan and after 300 ms the user put id input, the form will check the database if there is a duplication in the database or not. it will check all existingAmil, existingAdministrator table for id

-give data berhasil disimpan message if the data is sucessfully put into the database

## Amil account previlege:

-amil account can only access zakat fitrah, zakat maal, fidyah, infaq, shodaqoh, and pengeluaran page, export and import, riwayat zakat page, and cetak laporan page

-amil account cannot access admin account creation page or the tambah akun amil page.

-amil account cannot access administrator account.

-amil account cannot access amil account creation page or the tambah akun amil page.

---

# for zakat app v 0.1.4:

> this v 0.1.3 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding function for pengeluaran page.

## Pengeluaran function page:

-when user click simpan pengeluaran button, it saves the data into the pengeluaran table on database.
-when the user click simpan pengeluaran button, it will give data berhasil disimpan message if the data is sucessfully put into the database.
-give edit button in riwayat pengeluaran button to edit the data if the user wrongly input the data.

### The pengeluaran input schema:

-the Nama Pengeluaran will be into nama_pengeluaran in table

-the jumlah pengeluaran will be into jumlah_pengeluaran in table (since this column is not exist yet, add this jumlah_pengeluaran column into the pengeluaran table. this column is for the total pengeluaran)

-the zakat fitrah uang will be into zakat_fitrah_uang_outcome in table

-the zakat fitrah beras will be into zakat_fitrah_beras_outcome in table

-the zakat maal will be into zakat_maal_outcome in table

-the fidyah will be into fidyah_outcome in table

-the infaq will be into infaq_outcome in table

-the shodaqoh will be into shodaqoh_outcome in table

---

# for zakat app v 0.1.5:

> this v 0.1.3 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding function for amil page.

## Amil function page:

-when user click simpan amil button, it saves the data into the amil table on database.
-the daftar amil & volunteer sections shows data from the amil table on database.
-when the user click simpan amil button, it will give data berhasil disimpan message if the data is sucessfully put into the database.
-give edit button in riwayat pengeluaran button to edit the data if the user wrongly input the data.

### Amil input database schema:

-nama amil will be into nama_amil

-absen amil will be into absen_amil

-jatah amil will be into jatah_amil

-fee dasar amil will be into fee_dasar_amil

-fee total amil will be into fee_total_amil

### The fee total amil calculation:

-fee total amil = absen amil \* fee dasar amil

-total fee of all total amil cannot exceed jatah amil jatah_amil, if total fee amil exceeded jatah amil, give error message 'fee amil melewati jatah amil' with red color so the user will notice it easier. So make the calculation to sum all the fee total amil and check if it exceeds jatah amil.

---

# for zakat app v 0.1.6:

> this v 0.1.3 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding function for lihat laporan page. This laporan page will list all the income, outcome data and net balance from zakat fitrah, zakat maal, fidyah, infaq, shodaqoh, and pengeluaran page. the net balance is calculated by subtracting the total outcome from the total income.

## lihat laporan page:

-for total income in zakat fitrah uang, it will sum up all the transaction in zakat fitrah uang income.

-for total income in zakat fitrah beras, it will sum up all the transaction in zakat fitrah beras income.

-for total income in zakat maal, it will sum up all the transaction in zakat maal income.

-for total income in fidyah, it will sum up all the transaction in fidyah income.

-for total income in infaq, it will sum up all the transaction in infaq income.

-for total income in shodaqoh, it will sum up all the transaction in shodaqoh income.

-for total outcome in zakat fitrah uang, it will sum up all the transaction in pengeluaran zakat fitrah uang outcome.

-for total outcome in zakat fitrah beras, it will sum up all the transaction in pengeluaran zakat fitrah beras outcome.

-for total outcome in zakat maal, it will sum up all the transaction in pengeluaran zakat maal outcome.

-for total outcome in fidyah, it will sum up all the transaction in pengeluaran fidyah outcome.

-for total outcome in infaq, it will sum up all the transaction in pengeluaran infaq outcome.

-for total outcome in shodaqoh, it will sum up all the transaction in pengeluaran shodaqoh outcome.

-for net balance in zakat fitrah uang, it will subtract the total outcome zakat fitrah uang from the total income zakat fitrah uang.

-for net balance in zakat fitrah beras, it will subtract the total outcome zakat fitrah beras from the total income zakat fitrah beras.

-for net balance in zakat maal, it will subtract the total outcome zakat maal from the total income zakat maal.

-for net balance in fidyah, it will subtract the total outcome fidyah from the total income fidyah.

-for net balance in infaq, it will subtract the total outcome infaq from the total income infaq.

-for net balance in shodaqoh, it will subtract the total outcome shodaqoh from the total income shodaqoh.

---

# for zakat app v 0.1.7:

> this v 0.1.7 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding function for import and export data

# for zakat app v 0.1.8:

> this v 0.1.7 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> adding function to cetak laporan on cetak laporan page

## cetak laporan page:

-this page have feature to cetak laporan with export to pdf.
-this page have feature to cetak laporan with filter which laporan to be printed. this filter is checklist box.
-the general format or template when exporting the pdf is:
-title (for example "Laporan Zakat")
-date (for example "Tanggal: 2025-10-15")
-all income data from the date range (for example "Zakat Fitrah Uang: Rp 100.000")
-all outcome data from the date range (for example "Pengeluaran Zakat Fitrah Uang: Rp 50.000")
-net balance (for example "Saldo Net: Rp 50.000")

# for zakat app v 0.1.9-beta-1:

> this v 0.1.7 is **ALREADY IMPLEMENTED AND FUNCTIONING**
> make the design responsive for phone

-make all the design layout and UI responsive for phone

---

# for zakat app v 0.2.0-beta-1:

> **ALREADY IMPLEMENTED AND FUNCTIONING**
> performance optimization plan (post-cloud deployment) phase 1 - I will breakdown the performance optimization plan into several phases

## Phase 1 (high impact, low risk)

-limit history loads (pagination or fixed limit + load more) for all history pages to reduce payload size
-move aggregation to SQL (use `SUM()` in queries) for laporan/cetak-laporan so totals are computed in DB, not in JS
-cache report results per date range for short TTL (e.g., 30–60s) to reduce repeated heavy work
-optimize `checkIdNota` by reducing multi-table queries (single union query or short-lived server cache)

---

# for zakat app v 0.2.1-beta-1:

> **ALREADY IMPLEMENTED AND FUNCTIONING**
> performance optimization plan (post-cloud deployment) phase 2

## Phase 2 (medium impact)

-add/verify indexes on `created_at` for all transaction tables to speed order + range filters
-defer non-critical data loads so UI renders fast before secondary data arrives

---

# for zakat app v 0.2.2-beta-1:

> **ALREADY IMPLEMENTED AND FUNCTIONING**
> performance optimization plan (post-cloud deployment) phase 3

## Phase 3 (optional, scalable)

-add pre-aggregated summary tables (daily/monthly) for laporan to keep queries constant time
-run large exports as background jobs and provide a download link when ready

---

# for zakat app v -0.2.3-beta-1:

> **ALREADY IMPLEMENTED AND FUNCTIONING**
> added function to check only one entry data inputted per ID nota

- for zakat page, make sure the user only input one entry data per ID nota, it is either zakat fitrah uang or zakat fitrah beras.
- for fidyah page, make sure the user only input one entry data per ID nota, it is either fidyah uang or fidyah beras.
- for pengeluaran page, make sure the user only input one entry data per ID nota, zakat fitrah uang outcome, zakat fitrah beras outcome, fidyah uang outcome, fidyah beras outcome, zakat maal uang outcome, infaq uang outcome, or shodaqoh uang outcome.
- this is to make sure there is only one entry data per ID nota so the user can't input multiple entry data for the same ID nota.
- if the user try to input multiple entry data for the same ID nota, show a warning message 'pastikan hanya input satu data saja' below the input field (just like ID nota input field error message).
