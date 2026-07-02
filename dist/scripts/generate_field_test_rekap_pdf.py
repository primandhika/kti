from __future__ import annotations

import html
import shutil
import subprocess
from pathlib import Path

import pandas as pd


ROOT = Path(__file__).resolve().parents[1]
PRETEST = ROOT / "data" / "field_test" / "keterampilan_berbicara_pretes.csv"
POSTTEST = ROOT / "data" / "field_test" / "keterampilan_berbicara_postes.csv"
OUT_DIR = ROOT / "outputs"
OUT_HTML = OUT_DIR / "rekapitulasi_tes_keterampilan_berbicara_field_test.html"
OUT_PDF = OUT_DIR / "rekapitulasi_tes_keterampilan_berbicara_field_test.pdf"
OUT_CSV = OUT_DIR / "rekapitulasi_tes_keterampilan_berbicara_field_test.csv"


def fmt_decimal(value: float, digits: int = 2) -> str:
    return f"{value:.{digits}f}".replace(".", ",")


def fmt_gain(value: float) -> str:
    return f"{value:.3f}".replace(".", ",")


def category_ngain(value: float) -> str:
    if value >= 0.70:
        return "Tinggi"
    if value >= 0.30:
        return "Sedang"
    return "Rendah"


def load_recap() -> pd.DataFrame:
    pre = pd.read_csv(PRETEST)
    post = pd.read_csv(POSTTEST)

    required_pre = {"id", "kelompok", "pre_nilai_akhir"}
    required_post = {"id", "kelompok", "post_nilai_akhir"}
    missing_pre = required_pre - set(pre.columns)
    missing_post = required_post - set(post.columns)
    if missing_pre or missing_post:
        raise ValueError(f"Kolom tidak lengkap. Pretes: {missing_pre}; postes: {missing_post}")

    if pre["id"].duplicated().any() or post["id"].duplicated().any():
        raise ValueError("Terdapat kode subjek duplikat pada data pretes atau postes.")

    df = pre[["id", "kelompok", "pre_nilai_akhir"]].merge(
        post[["id", "kelompok", "post_nilai_akhir"]],
        on="id",
        how="inner",
        suffixes=("_pre", "_post"),
    )

    if len(df) != len(pre) or len(df) != len(post):
        raise ValueError("Jumlah pasangan pretes-postes tidak sama dengan data sumber.")
    if not (df["kelompok_pre"] == df["kelompok_post"]).all():
        raise ValueError("Ada subjek dengan label kelompok yang berubah antara pretes dan postes.")

    df = df.rename(
        columns={
            "id": "kode_subjek",
            "kelompok_pre": "kelompok",
            "pre_nilai_akhir": "pre",
            "post_nilai_akhir": "post",
        }
    ).drop(columns=["kelompok_post"])
    df["gain"] = df["post"] - df["pre"]
    df["n_gain"] = df["gain"] / (100 - df["pre"])
    df["kategori_n_gain"] = df["n_gain"].map(category_ngain)

    order = {"eksperimen": 0, "kontrol": 1}
    df["_order"] = df["kelompok"].map(order).fillna(9)
    df = df.sort_values(["_order", "kode_subjek"]).drop(columns=["_order"]).reset_index(drop=True)
    df.insert(0, "no", range(1, len(df) + 1))
    return df


def group_summary(df: pd.DataFrame) -> pd.DataFrame:
    rows = []
    for group_name, group in df.groupby("kelompok", sort=False):
        rows.append(
            {
                "kelompok": group_name.capitalize(),
                "n": len(group),
                "pre_mean": group["pre"].mean(),
                "pre_sd": group["pre"].std(ddof=1),
                "post_mean": group["post"].mean(),
                "post_sd": group["post"].std(ddof=1),
                "gain_mean": group["gain"].mean(),
                "gain_sd": group["gain"].std(ddof=1),
                "ngain_mean": group["n_gain"].mean(),
                "ngain_cat": category_ngain(group["n_gain"].mean()),
            }
        )
    return pd.DataFrame(rows)


def overall_summary(df: pd.DataFrame) -> dict[str, float | int | str]:
    return {
        "n": len(df),
        "pre_mean": df["pre"].mean(),
        "post_mean": df["post"].mean(),
        "gain_mean": df["gain"].mean(),
        "ngain_mean": df["n_gain"].mean(),
        "ngain_cat": category_ngain(df["n_gain"].mean()),
    }


def render_summary_table(summary: pd.DataFrame) -> str:
    rows = []
    for _, row in summary.iterrows():
        rows.append(
            "<tr>"
            f"<td>{html.escape(str(row['kelompok']))}</td>"
            f"<td class=\"num\">{int(row['n'])}</td>"
            f"<td class=\"num\">{fmt_decimal(row['pre_mean'])} &plusmn; {fmt_decimal(row['pre_sd'])}</td>"
            f"<td class=\"num\">{fmt_decimal(row['post_mean'])} &plusmn; {fmt_decimal(row['post_sd'])}</td>"
            f"<td class=\"num\">{fmt_decimal(row['gain_mean'])} &plusmn; {fmt_decimal(row['gain_sd'])}</td>"
            f"<td class=\"num\">{fmt_gain(row['ngain_mean'])}</td>"
            f"<td>{html.escape(str(row['ngain_cat']))}</td>"
            "</tr>"
        )
    return "\n".join(rows)


def render_detail_rows(df: pd.DataFrame) -> str:
    rows = []
    for _, row in df.iterrows():
        rows.append(
            "<tr>"
            f"<td class=\"num\">{int(row['no'])}</td>"
            f"<td>{html.escape(str(row['kode_subjek']))}</td>"
            f"<td>{html.escape(str(row['kelompok']).capitalize())}</td>"
            f"<td class=\"num\">{fmt_decimal(row['pre'])}</td>"
            f"<td class=\"num\">{fmt_decimal(row['post'])}</td>"
            f"<td class=\"num\">{fmt_decimal(row['gain'])}</td>"
            f"<td class=\"num\">{fmt_gain(row['n_gain'])}</td>"
            f"<td>{html.escape(str(row['kategori_n_gain']))}</td>"
            "</tr>"
        )
    return "\n".join(rows)


def build_html(df: pd.DataFrame) -> str:
    summary = group_summary(df)
    overall = overall_summary(df)
    return f"""<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Rekapitulasi Pelaksanaan Tes Keterampilan Berbicara</title>
<style>
@page {{
  size: A4 landscape;
  margin: 9mm 9mm 8mm;
}}
* {{
  box-sizing: border-box;
}}
body {{
  margin: 0;
  color: #111;
  font-family: Cambria, Georgia, serif;
  font-size: 9.2pt;
  line-height: 1.23;
}}
h1 {{
  margin: 0 0 7px;
  text-align: center;
  font-size: 15pt;
  line-height: 1.15;
  font-weight: 700;
}}
h2 {{
  margin: 10px 0 4px;
  font-size: 10.5pt;
}}
p {{
  margin: 2px 0;
}}
.meta {{
  display: grid;
  grid-template-columns: 1.3fr 1fr;
  gap: 8px;
  margin-bottom: 7px;
}}
.box {{
  border: 0.8px solid #333;
  padding: 5px 7px;
}}
.formula {{
  font-size: 8.6pt;
}}
table {{
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}}
thead {{
  display: table-header-group;
}}
th, td {{
  border: 0.65px solid #333;
  padding: 2.2px 3px;
  vertical-align: middle;
  overflow-wrap: anywhere;
}}
th {{
  text-align: center;
  font-weight: 700;
  background: #eeeeee;
}}
.summary-table {{
  margin-top: 5px;
  font-size: 8.6pt;
}}
.detail-table {{
  font-size: 8.2pt;
}}
.detail-table th:nth-child(1), .detail-table td:nth-child(1) {{ width: 5%; }}
.detail-table th:nth-child(2), .detail-table td:nth-child(2) {{ width: 13%; }}
.detail-table th:nth-child(3), .detail-table td:nth-child(3) {{ width: 15%; }}
.detail-table th:nth-child(4), .detail-table td:nth-child(4) {{ width: 12%; }}
.detail-table th:nth-child(5), .detail-table td:nth-child(5) {{ width: 12%; }}
.detail-table th:nth-child(6), .detail-table td:nth-child(6) {{ width: 12%; }}
.detail-table th:nth-child(7), .detail-table td:nth-child(7) {{ width: 14%; }}
.detail-table th:nth-child(8), .detail-table td:nth-child(8) {{ width: 17%; }}
.num {{
  text-align: right;
  white-space: nowrap;
}}
.center {{
  text-align: center;
}}
.small {{
  font-size: 8.2pt;
}}
</style>
</head>
<body>
<h1>Rekapitulasi Pelaksanaan Tes Keterampilan Berbicara</h1>
<div class="meta">
  <div class="box formula">
    <p><strong>Sumber data:</strong> field test keterampilan berbicara, pasangan pretes-postes berdasarkan kode subjek.</p>
    <p><strong>Rumus skor akhir:</strong> (jumlah lima aspek rubrik / 75) &times; 100.</p>
    <p><strong>Gain:</strong> postes - pretes. <strong>N-Gain:</strong> (postes - pretes) / (100 - pretes).</p>
    <p><strong>Kategori N-Gain:</strong> tinggi &ge; 0,70; sedang 0,30-0,69; rendah &lt; 0,30.</p>
  </div>
  <div class="box">
    <p><strong>Jumlah subjek:</strong> {overall['n']} mahasiswa</p>
    <p><strong>Rerata pretes:</strong> {fmt_decimal(overall['pre_mean'])}</p>
    <p><strong>Rerata postes:</strong> {fmt_decimal(overall['post_mean'])}</p>
    <p><strong>Rerata gain:</strong> {fmt_decimal(overall['gain_mean'])}</p>
    <p><strong>Rerata N-Gain:</strong> {fmt_gain(overall['ngain_mean'])} ({overall['ngain_cat']})</p>
  </div>
</div>

<h2>Ringkasan Per Kelompok</h2>
<table class="summary-table">
  <thead>
    <tr>
      <th>Kelompok</th>
      <th>n</th>
      <th>Pretes, Mean &plusmn; SD</th>
      <th>Postes, Mean &plusmn; SD</th>
      <th>Gain, Mean &plusmn; SD</th>
      <th>Rerata N-Gain</th>
      <th>Kategori</th>
    </tr>
  </thead>
  <tbody>
    {render_summary_table(summary)}
  </tbody>
</table>

<h2>Data Rinci dan Perhitungan N-Gain</h2>
<table class="detail-table">
  <thead>
    <tr>
      <th>No.</th>
      <th>Kode Subjek</th>
      <th>Kelompok</th>
      <th>Pre</th>
      <th>Post</th>
      <th>Gain</th>
      <th>N-Gain</th>
      <th>Kategori</th>
    </tr>
  </thead>
  <tbody>
    {render_detail_rows(df)}
  </tbody>
</table>
<p class="small">Catatan: skor pre dan post menggunakan skala 0-100; N-Gain dihitung per subjek dari skor akhir masing-masing.</p>
</body>
</html>
"""


def write_csv(df: pd.DataFrame) -> None:
    export = df.copy()
    export["pre"] = export["pre"].round(2)
    export["post"] = export["post"].round(2)
    export["gain"] = export["gain"].round(2)
    export["n_gain"] = export["n_gain"].round(3)
    export.to_csv(OUT_CSV, index=False)


def find_chromium() -> str | None:
    candidates = [
        shutil.which("chrome"),
        shutil.which("msedge"),
        shutil.which("chromium"),
        r"C:\Program Files\Google\Chrome\Application\chrome.exe",
        r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
        r"C:\Program Files\Microsoft\Edge\Application\msedge.exe",
        r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
    ]
    for candidate in candidates:
        if candidate and Path(candidate).exists():
            return candidate
    return None


def render_pdf() -> None:
    browser = find_chromium()
    if browser is None:
        raise RuntimeError("Chrome/Edge tidak ditemukan untuk merender PDF.")

    file_url = OUT_HTML.resolve().as_uri()
    command = [
        browser,
        "--headless",
        "--disable-gpu",
        "--no-pdf-header-footer",
        f"--print-to-pdf={OUT_PDF}",
        file_url,
    ]
    subprocess.run(command, check=True, cwd=ROOT)


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    df = load_recap()
    write_csv(df)
    OUT_HTML.write_text(build_html(df), encoding="utf-8")
    render_pdf()
    print(f"Wrote: {OUT_PDF}")
    print(f"Wrote: {OUT_HTML}")
    print(f"Wrote: {OUT_CSV}")


if __name__ == "__main__":
    main()
