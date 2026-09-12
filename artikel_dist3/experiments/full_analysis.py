#!/usr/bin/env python3
"""
Full statistical analysis for CALL-EJ manuscript.
Produces baseline-adjusted ANCOVA, descriptive stats, reliability, effect sizes.
"""
import pandas as pd
import numpy as np
from scipy import stats
import json
import warnings
warnings.filterwarnings('ignore')

DATA_DIR = "/home/primandhika/artikel/dist/data"

# ============================================================
# 1. LOAD DATA
# ============================================================
pre_spk = pd.read_csv(f"{DATA_DIR}/field_test/keterampilan_berbicara_pretes.csv")
post_spk = pd.read_csv(f"{DATA_DIR}/field_test/keterampilan_berbicara_postes.csv")
meta = pd.read_csv(f"{DATA_DIR}/field_test/metakognitif.csv")
resp = pd.read_csv(f"{DATA_DIR}/field_test/respons_mahasiswa.csv")

# Merge speaking pre and post
spk = pre_spk[['id', 'kelompok', 'pre_nilai_akhir']].merge(
    post_spk[['id', 'post_nilai_akhir']], on='id'
)
spk.columns = ['id', 'group', 'pre_speaking', 'post_speaking']
spk['group_code'] = (spk['group'] == 'eksperimen').astype(int)
spk['gain_speaking'] = spk['post_speaking'] - spk['pre_speaking']

# Metacognitive
meta_clean = meta[['id', 'kelompok', 'pre_total', 'post_total']].copy()
meta_clean.columns = ['id', 'group', 'pre_meta', 'post_meta']
# Check for missing pre-test data
print("=== METACOGNITIVE DATA CHECK ===")
print(f"Total rows: {len(meta_clean)}")
print(f"Missing pre_meta: {meta_clean['pre_meta'].isna().sum()}")
print(f"Missing post_meta: {meta_clean['post_meta'].isna().sum()}")

# Some pre-test metacognitive values may be missing
# Keep only complete cases
meta_complete = meta_clean.dropna(subset=['pre_meta', 'post_meta']).copy()
meta_complete['group_code'] = (meta_complete['group'] == 'eksperimen').astype(int)
meta_complete['gain_meta'] = meta_complete['post_meta'] - meta_complete['pre_meta']

print(f"Complete meta cases: {len(meta_complete)}")
print(f"  Exp: {(meta_complete['group'] == 'eksperimen').sum()}")
print(f"  Con: {(meta_complete['group'] == 'kontrol').sum()}")

# ============================================================
# 2. DESCRIPTIVE STATISTICS - SPEAKING
# ============================================================
print("\n" + "="*60)
print("SPEAKING PERFORMANCE - DESCRIPTIVE STATISTICS")
print("="*60)

for grp_name, grp_label in [('eksperimen', 'Experimental'), ('kontrol', 'Control')]:
    g = spk[spk['group'] == grp_name]
    print(f"\n{grp_label} (n={len(g)}):")
    print(f"  Pre:  M={g['pre_speaking'].mean():.2f}, SD={g['pre_speaking'].std(ddof=1):.2f}, "
          f"Min={g['pre_speaking'].min():.2f}, Max={g['pre_speaking'].max():.2f}")
    print(f"  Post: M={g['post_speaking'].mean():.2f}, SD={g['post_speaking'].std(ddof=1):.2f}, "
          f"Min={g['post_speaking'].min():.2f}, Max={g['post_speaking'].max():.2f}")
    print(f"  Gain: M={g['gain_speaking'].mean():.2f}, SD={g['gain_speaking'].std(ddof=1):.2f}")

# Baseline standardized mean difference (Cohen's d for baseline comparison)
exp_pre = spk[spk['group'] == 'eksperimen']['pre_speaking']
con_pre = spk[spk['group'] == 'kontrol']['pre_speaking']
pooled_sd_pre = np.sqrt(((len(exp_pre)-1)*exp_pre.std(ddof=1)**2 + (len(con_pre)-1)*con_pre.std(ddof=1)**2) / (len(exp_pre)+len(con_pre)-2))
smd_pre_spk = (exp_pre.mean() - con_pre.mean()) / pooled_sd_pre
print(f"\nBaseline SMD (speaking): {smd_pre_spk:.3f}")
print(f"Baseline Welch t-test: t={stats.ttest_ind(exp_pre, con_pre, equal_var=False).statistic:.3f}, "
      f"p={stats.ttest_ind(exp_pre, con_pre, equal_var=False).pvalue:.4f}")

# ============================================================
# 3. DESCRIPTIVE STATISTICS - METACOGNITIVE
# ============================================================
print("\n" + "="*60)
print("METACOGNITIVE AWARENESS - DESCRIPTIVE STATISTICS")
print("="*60)

for grp_name, grp_label in [('eksperimen', 'Experimental'), ('kontrol', 'Control')]:
    g = meta_complete[meta_complete['group'] == grp_name]
    print(f"\n{grp_label} (n={len(g)}):")
    print(f"  Pre:  M={g['pre_meta'].mean():.2f}, SD={g['pre_meta'].std(ddof=1):.2f}, "
          f"Min={g['pre_meta'].min():.0f}, Max={g['pre_meta'].max():.0f}")
    print(f"  Post: M={g['post_meta'].mean():.2f}, SD={g['post_meta'].std(ddof=1):.2f}, "
          f"Min={g['post_meta'].min():.0f}, Max={g['post_meta'].max():.0f}")
    print(f"  Gain: M={g['gain_meta'].mean():.2f}, SD={g['gain_meta'].std(ddof=1):.2f}")

exp_pre_m = meta_complete[meta_complete['group'] == 'eksperimen']['pre_meta']
con_pre_m = meta_complete[meta_complete['group'] == 'kontrol']['pre_meta']
pooled_sd_pre_m = np.sqrt(((len(exp_pre_m)-1)*exp_pre_m.std(ddof=1)**2 + (len(con_pre_m)-1)*con_pre_m.std(ddof=1)**2) / (len(exp_pre_m)+len(con_pre_m)-2))
smd_pre_meta = (exp_pre_m.mean() - con_pre_m.mean()) / pooled_sd_pre_m
print(f"\nBaseline SMD (metacognitive): {smd_pre_meta:.3f}")
print(f"Baseline Welch t-test: t={stats.ttest_ind(exp_pre_m, con_pre_m, equal_var=False).statistic:.3f}, "
      f"p={stats.ttest_ind(exp_pre_m, con_pre_m, equal_var=False).pvalue:.4f}")

# ============================================================
# 4. BASELINE-ADJUSTED ANCOVA - SPEAKING (PRIMARY ANALYSIS)
# ============================================================
print("\n" + "="*60)
print("SPEAKING - BASELINE-ADJUSTED LINEAR MODEL (PRIMARY)")
print("="*60)

# Model: post_speaking = b0 + b1*group + b2*pre_speaking + error
from numpy.linalg import lstsq

X_spk = np.column_stack([
    np.ones(len(spk)),
    spk['group_code'].values,
    spk['pre_speaking'].values
])
y_spk = spk['post_speaking'].values

beta_spk, residuals_spk, rank_spk, sv_spk = lstsq(X_spk, y_spk, rcond=None)
y_hat_spk = X_spk @ beta_spk
resid_spk = y_spk - y_hat_spk
n_spk = len(y_spk)
k_spk = X_spk.shape[1]
df_resid_spk = n_spk - k_spk

# Standard errors (OLS)
mse_spk = np.sum(resid_spk**2) / df_resid_spk
cov_spk = mse_spk * np.linalg.inv(X_spk.T @ X_spk)
se_spk = np.sqrt(np.diag(cov_spk))

# HC3 robust standard errors
h_spk = np.diag(X_spk @ np.linalg.inv(X_spk.T @ X_spk) @ X_spk.T)
u_spk = resid_spk / (1 - h_spk)
bread_spk = np.linalg.inv(X_spk.T @ X_spk)
meat_spk = X_spk.T @ np.diag(u_spk**2) @ X_spk
hc3_cov_spk = bread_spk @ meat_spk @ bread_spk
hc3_se_spk = np.sqrt(np.diag(hc3_cov_spk))

# Group effect
adj_diff_spk = beta_spk[1]
se_adj_spk = se_spk[1]
se_adj_spk_hc3 = hc3_se_spk[1]
t_adj_spk = adj_diff_spk / se_adj_spk
t_adj_spk_hc3 = adj_diff_spk / se_adj_spk_hc3
p_adj_spk = 2 * stats.t.sf(abs(t_adj_spk), df_resid_spk)
p_adj_spk_hc3 = 2 * stats.t.sf(abs(t_adj_spk_hc3), df_resid_spk)
ci_lo_spk = adj_diff_spk - stats.t.ppf(0.975, df_resid_spk) * se_adj_spk
ci_hi_spk = adj_diff_spk + stats.t.ppf(0.975, df_resid_spk) * se_adj_spk
ci_lo_spk_hc3 = adj_diff_spk - stats.t.ppf(0.975, df_resid_spk) * se_adj_spk_hc3
ci_hi_spk_hc3 = adj_diff_spk + stats.t.ppf(0.975, df_resid_spk) * se_adj_spk_hc3

# Hedges' g for adjusted difference
# Use residual SD as denominator
resid_sd_spk = np.sqrt(mse_spk)
n1_spk = (spk['group'] == 'eksperimen').sum()
n2_spk = (spk['group'] == 'kontrol').sum()
# Hedges' g correction factor
j_spk = 1 - 3 / (4 * (n1_spk + n2_spk - 2) - 1)
hedges_g_spk = (adj_diff_spk / resid_sd_spk) * j_spk

print(f"Coefficients: intercept={beta_spk[0]:.3f}, group={beta_spk[1]:.3f}, pre_speaking={beta_spk[2]:.3f}")
print(f"Adjusted group difference: {adj_diff_spk:.2f}")
print(f"OLS SE: {se_adj_spk:.3f}, t={t_adj_spk:.3f}, p={p_adj_spk:.5f}")
print(f"OLS 95% CI: [{ci_lo_spk:.2f}, {ci_hi_spk:.2f}]")
print(f"HC3 SE: {se_adj_spk_hc3:.3f}, t={t_adj_spk_hc3:.3f}, p={p_adj_spk_hc3:.5f}")
print(f"HC3 95% CI: [{ci_lo_spk_hc3:.2f}, {ci_hi_spk_hc3:.2f}]")
print(f"Residual SD: {resid_sd_spk:.3f}")
print(f"Hedges' g (adjusted): {hedges_g_spk:.3f}")
print(f"R²: {1 - np.sum(resid_spk**2) / np.sum((y_spk - y_spk.mean())**2):.4f}")

# Sensitivity: Welch t-test on gains
exp_gain_spk = spk[spk['group'] == 'eksperimen']['gain_speaking']
con_gain_spk = spk[spk['group'] == 'kontrol']['gain_speaking']
welch_spk = stats.ttest_ind(exp_gain_spk, con_gain_spk, equal_var=False)
gain_diff_spk = exp_gain_spk.mean() - con_gain_spk.mean()
# Welch CI
se_welch_spk = np.sqrt(exp_gain_spk.var(ddof=1)/len(exp_gain_spk) + con_gain_spk.var(ddof=1)/len(con_gain_spk))
df_welch_spk = welch_spk.df if hasattr(welch_spk, 'df') else (exp_gain_spk.var(ddof=1)/len(exp_gain_spk) + con_gain_spk.var(ddof=1)/len(con_gain_spk))**2 / ((exp_gain_spk.var(ddof=1)/len(exp_gain_spk))**2/(len(exp_gain_spk)-1) + (con_gain_spk.var(ddof=1)/len(con_gain_spk))**2/(len(con_gain_spk)-1))
ci_lo_welch_spk = gain_diff_spk - stats.t.ppf(0.975, df_welch_spk) * se_welch_spk
ci_hi_welch_spk = gain_diff_spk + stats.t.ppf(0.975, df_welch_spk) * se_welch_spk
# Hedges' g for gain
pooled_sd_gain_spk = np.sqrt(((len(exp_gain_spk)-1)*exp_gain_spk.std(ddof=1)**2 + (len(con_gain_spk)-1)*con_gain_spk.std(ddof=1)**2) / (len(exp_gain_spk)+len(con_gain_spk)-2))
hedges_g_gain_spk = (gain_diff_spk / pooled_sd_gain_spk) * j_spk

print(f"\nSensitivity: Welch t-test on gains")
print(f"  Mean gain diff: {gain_diff_spk:.2f}")
print(f"  t={welch_spk.statistic:.3f}, df={df_welch_spk:.1f}, p={welch_spk.pvalue:.5f}")
print(f"  95% CI: [{ci_lo_welch_spk:.2f}, {ci_hi_welch_spk:.2f}]")
print(f"  Hedges' g (gain): {hedges_g_gain_spk:.3f}")

# ============================================================
# 5. BASELINE-ADJUSTED ANCOVA - METACOGNITIVE (PRIMARY)
# ============================================================
print("\n" + "="*60)
print("METACOGNITIVE - BASELINE-ADJUSTED LINEAR MODEL (PRIMARY)")
print("="*60)

X_meta = np.column_stack([
    np.ones(len(meta_complete)),
    meta_complete['group_code'].values,
    meta_complete['pre_meta'].values
])
y_meta = meta_complete['post_meta'].values

beta_meta, residuals_meta, rank_meta, sv_meta = lstsq(X_meta, y_meta, rcond=None)
y_hat_meta = X_meta @ beta_meta
resid_meta = y_meta - y_hat_meta
n_meta = len(y_meta)
k_meta = X_meta.shape[1]
df_resid_meta = n_meta - k_meta

mse_meta = np.sum(resid_meta**2) / df_resid_meta
cov_meta = mse_meta * np.linalg.inv(X_meta.T @ X_meta)
se_meta = np.sqrt(np.diag(cov_meta))

# HC3
h_meta = np.diag(X_meta @ np.linalg.inv(X_meta.T @ X_meta) @ X_meta.T)
u_meta = resid_meta / (1 - h_meta)
bread_meta = np.linalg.inv(X_meta.T @ X_meta)
meat_meta = X_meta.T @ np.diag(u_meta**2) @ X_meta
hc3_cov_meta = bread_meta @ meat_meta @ bread_meta
hc3_se_meta = np.sqrt(np.diag(hc3_cov_meta))

adj_diff_meta = beta_meta[1]
se_adj_meta = se_meta[1]
se_adj_meta_hc3 = hc3_se_meta[1]
t_adj_meta = adj_diff_meta / se_adj_meta
t_adj_meta_hc3 = adj_diff_meta / se_adj_meta_hc3
p_adj_meta = 2 * stats.t.sf(abs(t_adj_meta), df_resid_meta)
p_adj_meta_hc3 = 2 * stats.t.sf(abs(t_adj_meta_hc3), df_resid_meta)
ci_lo_meta = adj_diff_meta - stats.t.ppf(0.975, df_resid_meta) * se_adj_meta
ci_hi_meta = adj_diff_meta + stats.t.ppf(0.975, df_resid_meta) * se_adj_meta
ci_lo_meta_hc3 = adj_diff_meta - stats.t.ppf(0.975, df_resid_meta) * se_adj_meta_hc3
ci_hi_meta_hc3 = adj_diff_meta + stats.t.ppf(0.975, df_resid_meta) * se_adj_meta_hc3

resid_sd_meta = np.sqrt(mse_meta)
n1_meta = (meta_complete['group'] == 'eksperimen').sum()
n2_meta = (meta_complete['group'] == 'kontrol').sum()
j_meta = 1 - 3 / (4 * (n1_meta + n2_meta - 2) - 1)
hedges_g_meta = (adj_diff_meta / resid_sd_meta) * j_meta

print(f"Coefficients: intercept={beta_meta[0]:.3f}, group={beta_meta[1]:.3f}, pre_meta={beta_meta[2]:.3f}")
print(f"Adjusted group difference: {adj_diff_meta:.2f}")
print(f"OLS SE: {se_adj_meta:.3f}, t={t_adj_meta:.3f}, p={p_adj_meta:.5f}")
print(f"OLS 95% CI: [{ci_lo_meta:.2f}, {ci_hi_meta:.2f}]")
print(f"HC3 SE: {se_adj_meta_hc3:.3f}, t={t_adj_meta_hc3:.3f}, p={p_adj_meta_hc3:.5f}")
print(f"HC3 95% CI: [{ci_lo_meta_hc3:.2f}, {ci_hi_meta_hc3:.2f}]")
print(f"Residual SD: {resid_sd_meta:.3f}")
print(f"Hedges' g (adjusted): {hedges_g_meta:.3f}")
print(f"R²: {1 - np.sum(resid_meta**2) / np.sum((y_meta - y_meta.mean())**2):.4f}")

# Sensitivity: Welch t-test on gains
exp_gain_meta = meta_complete[meta_complete['group'] == 'eksperimen']['gain_meta']
con_gain_meta = meta_complete[meta_complete['group'] == 'kontrol']['gain_meta']
welch_meta = stats.ttest_ind(exp_gain_meta, con_gain_meta, equal_var=False)
gain_diff_meta = exp_gain_meta.mean() - con_gain_meta.mean()
se_welch_meta = np.sqrt(exp_gain_meta.var(ddof=1)/len(exp_gain_meta) + con_gain_meta.var(ddof=1)/len(con_gain_meta))
df_welch_meta = (exp_gain_meta.var(ddof=1)/len(exp_gain_meta) + con_gain_meta.var(ddof=1)/len(con_gain_meta))**2 / ((exp_gain_meta.var(ddof=1)/len(exp_gain_meta))**2/(len(exp_gain_meta)-1) + (con_gain_meta.var(ddof=1)/len(con_gain_meta))**2/(len(con_gain_meta)-1))
ci_lo_welch_meta = gain_diff_meta - stats.t.ppf(0.975, df_welch_meta) * se_welch_meta
ci_hi_welch_meta = gain_diff_meta + stats.t.ppf(0.975, df_welch_meta) * se_welch_meta
pooled_sd_gain_meta = np.sqrt(((len(exp_gain_meta)-1)*exp_gain_meta.std(ddof=1)**2 + (len(con_gain_meta)-1)*con_gain_meta.std(ddof=1)**2) / (len(exp_gain_meta)+len(con_gain_meta)-2))
hedges_g_gain_meta = (gain_diff_meta / pooled_sd_gain_meta) * j_meta

print(f"\nSensitivity: Welch t-test on gains")
print(f"  Mean gain diff: {gain_diff_meta:.2f}")
print(f"  t={welch_meta.statistic:.3f}, df={df_welch_meta:.1f}, p={welch_meta.pvalue:.5f}")
print(f"  95% CI: [{ci_lo_welch_meta:.2f}, {ci_hi_welch_meta:.2f}]")
print(f"  Hedges' g (gain): {hedges_g_gain_meta:.3f}")

# ============================================================
# 6. CRONBACH'S ALPHA - METACOGNITIVE SCALE
# ============================================================
print("\n" + "="*60)
print("METACOGNITIVE INSTRUMENT RELIABILITY (CRONBACH'S ALPHA)")
print("="*60)

# Load item-level data from the original formatted CSV
# The current meta CSV only has totals per dimension. 
# Let me check if item-level data exists in the _history
try:
    sot_meta = pd.read_csv(f"{DATA_DIR}/_history/[SoT] angket-metakognitif.csv")
    print("Found SoT angket-metakognitif.csv")
    print(f"Columns: {list(sot_meta.columns)}")
    print(f"Rows: {len(sot_meta)}")
except:
    print("SoT file not found, trying alternative...")

# Cronbach's alpha from subscale totals (4 subscales)
def cronbachs_alpha(items_df):
    """Calculate Cronbach's alpha from item scores."""
    k = items_df.shape[1]
    item_vars = items_df.var(ddof=1)
    total_var = items_df.sum(axis=1).var(ddof=1)
    alpha = (k / (k - 1)) * (1 - item_vars.sum() / total_var)
    return alpha

# Using subscale totals as "items" for composite reliability
# Pre-test
exp_pre_meta_subs = meta[meta['kelompok'] == 'eksperimen'][['pre_planning', 'pre_monitoring', 'pre_evaluation', 'pre_integratif']].dropna()
con_pre_meta_subs = meta[meta['kelompok'] == 'kontrol'][['pre_planning', 'pre_monitoring', 'pre_evaluation', 'pre_integratif']]
all_pre_meta_subs = meta[['pre_planning', 'pre_monitoring', 'pre_evaluation', 'pre_integratif']].dropna()

exp_post_meta_subs = meta[meta['kelompok'] == 'eksperimen'][['post_planning', 'post_monitoring', 'post_evaluation', 'post_integratif']].dropna()
con_post_meta_subs = meta[meta['kelompok'] == 'kontrol'][['post_planning', 'post_monitoring', 'post_evaluation', 'post_integratif']]
all_post_meta_subs = meta[['post_planning', 'post_monitoring', 'post_evaluation', 'post_integratif']].dropna()

# Alpha using subscale totals as items
alpha_pre_all = cronbachs_alpha(all_pre_meta_subs) if len(all_pre_meta_subs) > 2 else np.nan
alpha_post_all = cronbachs_alpha(all_post_meta_subs)

print(f"\nCronbach's alpha (subscale-level, 4 subscales):")
print(f"  Pretest (all with data, n={len(all_pre_meta_subs)}): α = {alpha_pre_all:.3f}")
print(f"  Posttest (all, n={len(all_post_meta_subs)}): α = {alpha_post_all:.3f}")

# Check how many experimental have pre_meta data
exp_with_pre = meta[meta['kelompok'] == 'eksperimen'].dropna(subset=['pre_planning', 'pre_monitoring', 'pre_evaluation', 'pre_integratif'])
print(f"\n  Exp students with pre-test metacognitive data: {len(exp_with_pre)}")

# ============================================================
# 7. POST-USE QUESTIONNAIRE ANALYSIS
# ============================================================
print("\n" + "="*60)
print("POST-USE PERCEPTION QUESTIONNAIRE")
print("="*60)

# Scale: each dimension has 2 items on 1-4 scale, so max per dimension = 8
dims = ['kualitas_konten', 'kemudahan_penggunaan', 'keterlibatan', 'dampak_berbicara',
        'dukungan_metakognisi', 'microlearning', 'aksesibilitas', 'teknik_feynman', 'refleksi_diri']

print(f"\nn = {len(resp)}")
print(f"\nDimension stats (each dimension max = 8, i.e., 2 items × 4-point scale):")
for dim in dims:
    m = resp[dim].mean()
    sd = resp[dim].std(ddof=1)
    print(f"  {dim:25s}: M={m:.2f}, SD={sd:.2f}, M/8={m/8*100:.1f}%")

total_m = resp['total_skor'].mean()
total_sd = resp['total_skor'].std(ddof=1)
pct_m = resp['persentase'].mean()
pct_sd = resp['persentase'].std(ddof=1)
print(f"\n  Total score:  M={total_m:.2f}, SD={total_sd:.2f}")
print(f"  Percentage:   M={pct_m:.2f}%, SD={pct_sd:.2f}%")

# Item-level means (converting dimension scores to per-item means, 2 items per dim)
print(f"\nPer-item means (divide dimension score by 2):")
for dim in dims:
    item_m = resp[dim].mean() / 2
    print(f"  {dim:25s}: per-item M={item_m:.2f} (out of 4)")

# ============================================================
# 8. NORMALITY CHECK FOR ANCOVA RESIDUALS
# ============================================================
print("\n" + "="*60)
print("MODEL DIAGNOSTICS")
print("="*60)

# Speaking model residuals
sw_spk = stats.shapiro(resid_spk)
print(f"Speaking residuals - Shapiro-Wilk: W={sw_spk.statistic:.4f}, p={sw_spk.pvalue:.4f}")

# Metacognitive model residuals
sw_meta = stats.shapiro(resid_meta)
print(f"Meta residuals - Shapiro-Wilk: W={sw_meta.statistic:.4f}, p={sw_meta.pvalue:.4f}")

# Levene's test for homogeneity of variance on residuals by group
exp_resid_spk = resid_spk[spk['group_code'].values == 1]
con_resid_spk = resid_spk[spk['group_code'].values == 0]
lev_spk = stats.levene(exp_resid_spk, con_resid_spk)
print(f"Speaking residuals - Levene's: F={lev_spk.statistic:.4f}, p={lev_spk.pvalue:.4f}")

exp_resid_meta = resid_meta[meta_complete['group_code'].values == 1]
con_resid_meta = resid_meta[meta_complete['group_code'].values == 0]
lev_meta = stats.levene(exp_resid_meta, con_resid_meta)
print(f"Meta residuals - Levene's: F={lev_meta.statistic:.4f}, p={lev_meta.pvalue:.4f}")

# ============================================================
# 9. ADDITIONAL CHECKS
# ============================================================
print("\n" + "="*60)
print("ADDITIONAL DATA NOTES")
print("="*60)

# Rater info from speaking data
print("\nSpeaking raters (pretest):")
print(pre_spk['pre_penilai'].value_counts().to_string())
print("\nSpeaking raters (posttest):")
print(post_spk['post_penilai'].value_counts().to_string())

# NIM analysis for age/year estimation
print("\nNIM patterns:")
nims = pre_spk['nim'].astype(str)
years = nims.str[:3].value_counts()
print(years.to_string())

# Interview data summary
wawancara = pd.read_csv(f"{DATA_DIR}/kualitatif/wawancara.csv")
student_interviews = wawancara[wawancara['kode_subjek'].str.startswith('E', na=False)]
lecturer_interviews = wawancara[wawancara['kode_subjek'].str.startswith('D', na=False)]
print(f"\nQualitative data:")
print(f"  Student interview excerpts: {len(student_interviews)} from {student_interviews['kode_subjek'].nunique()} students")
print(f"  Lecturer field statements: {len(lecturer_interviews)} from {lecturer_interviews['kode_subjek'].nunique()} lecturers")

# Control contamination from lecturer interview
print("\nControl contamination (from D02 interview):")
print("  D02 reports: ~4-5 students tried Bicaranta voluntarily")

# ============================================================
# 10. SUMMARY FOR MANUSCRIPT PLACEHOLDERS
# ============================================================
print("\n" + "="*60)
print("MANUSCRIPT PLACEHOLDER VALUES")
print("="*60)

results = {
    "speaking": {
        "n_exp": int(n1_spk),
        "n_con": int(n2_spk),
        "exp_pre_M": round(exp_pre.mean(), 2),
        "exp_pre_SD": round(exp_pre.std(ddof=1), 2),
        "con_pre_M": round(con_pre.mean(), 2),
        "con_pre_SD": round(con_pre.std(ddof=1), 2),
        "exp_post_M": round(spk[spk['group']=='eksperimen']['post_speaking'].mean(), 2),
        "exp_post_SD": round(spk[spk['group']=='eksperimen']['post_speaking'].std(ddof=1), 2),
        "con_post_M": round(spk[spk['group']=='kontrol']['post_speaking'].mean(), 2),
        "con_post_SD": round(spk[spk['group']=='kontrol']['post_speaking'].std(ddof=1), 2),
        "adj_diff": round(adj_diff_spk, 2),
        "adj_ci_lo": round(ci_lo_spk, 2),
        "adj_ci_hi": round(ci_hi_spk, 2),
        "adj_p": round(p_adj_spk, 5),
        "adj_hedges_g": round(hedges_g_spk, 2),
        "gain_diff": round(gain_diff_spk, 2),
        "gain_ci_lo": round(ci_lo_welch_spk, 2),
        "gain_ci_hi": round(ci_hi_welch_spk, 2),
        "gain_t": round(welch_spk.statistic, 3),
        "gain_p": round(welch_spk.pvalue, 5),
        "gain_hedges_g": round(hedges_g_gain_spk, 2),
    },
    "metacognitive": {
        "n_exp": int(n1_meta),
        "n_con": int(n2_meta),
        "adj_diff": round(adj_diff_meta, 2),
        "adj_ci_lo": round(ci_lo_meta, 2),
        "adj_ci_hi": round(ci_hi_meta, 2),
        "adj_p": round(p_adj_meta, 5),
        "adj_hedges_g": round(hedges_g_meta, 2),
        "gain_diff": round(gain_diff_meta, 2),
        "gain_ci_lo": round(ci_lo_welch_meta, 2),
        "gain_ci_hi": round(ci_hi_welch_meta, 2),
        "gain_t": round(welch_meta.statistic, 3),
        "gain_p": round(welch_meta.pvalue, 5),
        "gain_hedges_g": round(hedges_g_gain_meta, 2),
    },
    "reliability": {
        "alpha_pre_subscale": round(alpha_pre_all, 3) if not np.isnan(alpha_pre_all) else "insufficient_data",
        "alpha_post_subscale": round(alpha_post_all, 3),
    }
}

print(json.dumps(results, indent=2))

# Save results
with open('experiments/analysis_results.json', 'w') as f:
    json.dump(results, f, indent=2)
print("\nResults saved to experiments/analysis_results.json")
