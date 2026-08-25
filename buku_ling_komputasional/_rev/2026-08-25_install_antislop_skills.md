# Catatan Revisi: Instalasi antislop Skills

## Perubahan
- Menginstal skill `antislop`, `antislop-copywriting`, dan `antislop-human` dari repositori `miqdadbadjuber/anti-slop`.
- Skill dipasang secara lokal pada proyek di folder `.agents/skills/`.

## Lokasi
- `.agents/skills/antislop/`
- `.agents/skills/antislop-copywriting/`
- `.agents/skills/antislop-human/`

## Alasan
- `antislop-copywriting` dan `antislop-human` memang diminta untuk membantu kualitas penulisan dan sisi human/accessibility.
- Repositori upstream menyatakan kedua skill tersebut dipakai bersama skill inti `antislop`, jadi skill inti ikut dipasang agar dependensi konseptualnya lengkap.

## Catatan
- Instalasi dilakukan dengan `npx skills add ... --skill ... --agent codex -y`.
- Folder hasil instalasi yang dibuat oleh alat ini berada di `.agents/skills/` pada proyek ini.
