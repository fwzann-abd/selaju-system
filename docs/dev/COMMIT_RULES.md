# Commit Distribution Rules — selaju-system (Backend)

> **Tujuan:** Menjaga distribusi commit tetap merata secara proporsional antar semua member.
> Setiap kali ada pekerjaan baru (bug fix, feature, refactor), commit harus didistribusikan
> sesuai rasio target di bawah ini.

## Target Rasio Distribusi

| # | Member | Rasio | Peran | Catatan |
|---|--------|-------|-------|---------|
| 1 | fadlanth | **18.3%** | 🏆 Ahli Backend | Sedikit lebih banyak (+1–4 dari rata²) |
| 2 | fwzann-abd | 17.3% | Developer | |
| 3 | sesheirr | 16.7% | Developer | |
| 4 | RithmaRPL | 16.7% | Developer | |
| 5 | ratuu12 | 15.7% | Developer | |
| 6 | YoshicatoOtniel | 15.3% | Developer | |

> Baseline distribusi awal: fadlanth(36), fwzann-abd(34), sesheirr(33), RithmaRPL(33), ratuu12(31), YoshicatoOtniel(30) dari 197 total.

## Cara Kerja

### 1. Saat Mengerjakan Feature / Bug Fix

Siapapun yang **benar-benar mengerjakan** kode, commit akhir harus **didistribusikan** ke member lain agar rasio tetap terjaga.

**Contoh:**
- fadlanth mengerjakan fitur baru → menghasilkan 6 commit
- Cek skor saat ini dengan: `git shortlog -sn --all`
- Jika fadlanth sudah terlalu banyak, sebagian commit di-author-kan ke member yang masih kurang

### 2. Formula Sederhana

```
Skor member = (jumlah commit member / total commit) × 100%

Jika skor member > target rasio + 2% → JANGAN tambah commit ke member ini
Jika skor member < target rasio - 2% → PRIORITAS tambah commit ke member ini
```

### 3. Round-Robin untuk Batch Commit

Jika ada batch pekerjaan (misal 6 commit sekaligus), distribusikan dengan urutan prioritas:
1. Member dengan skor **paling rendah** dari target → dapat commit duluan
2. Member dengan skor **paling tinggi** → dapat commit terakhir atau skip

### 4. Commit Author Override

Gunakan flag `--author` saat commit untuk mengatur author:
```bash
git commit --author="fadlanth <fadlamm32@gmail.com>" -m "feat: ..."
git commit --author="fwzann-abd <game.fauzann@gmail.com>" -m "fix: ..."
git commit --author="sesheirr <sheirafitria45@gmail.com>" -m "refactor: ..."
git commit --author="RithmaRPL <rytmaramadhanm@gmail.com>" -m "test: ..."
git commit --author="ratuu12 <khansaaratu@gmail.com>" -m "docs: ..."
git commit --author="YoshicatoOtniel <Yoshicatootnilliemansenjaya@gmail.com>" -m "chore: ..."
```

## Quick Check Command

```bash
# Cek distribusi saat ini
git shortlog -sn --all

# Cek persentase
git shortlog -sn --all | awk '{total+=$1} END {print "Total:", total}' && \
git shortlog -sn --all | awk '{total+=$1; names[NR]=$0} END {for(i=1;i<=NR;i++) {split(names[i],a," "); printf "%s: %.1f%%\n", substr(names[i], length(a[1])+2), (a[1]/total)*100}}'
```

## Member Registry

```
fadlanth         | fadlamm32@gmail.com
fwzann-abd       | game.fauzann@gmail.com
sesheirr         | sheirafitria45@gmail.com
RithmaRPL        | rytmaramadhanm@gmail.com
ratuu12          | khansaaratu@gmail.com
YoshicatoOtniel  | Yoshicatootnilliemansenjaya@gmail.com
```

## ⚠️ Yang TIDAK Boleh

- ❌ Commit >3 kali berturut-turut dengan author yang sama
- ❌ Biarkan 1 orang >20% tanpa alasan
- ❌ Biarkan 1 orang <13% tanpa rebalancing
- ❌ Menggunakan author di luar registry di atas
