<?php
/**
 * កម្មវិធីគ្រប់គ្រងទិន្នន័យនាយទាហាន (Military Personnel Management System)
 * ភាសា PHP / HTML5 / CSS3 / JavaScript
 */
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ប្រព័ន្ធគ្រប់គ្រងទិន្នន័យនាយទាហាន (Military Personnel Management System)</title>
    <!-- PWA & Mobile Web App Meta Tags -->
    <meta name="theme-color" content="#003366">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="បុគ្គលិកយោធា">
    <link rel="manifest" href="manifest.json">
    
    <!-- Google Fonts Kantumruy Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
/* ==========================================================================
   ប្រព័ន្ធគ្រប់គ្រងទិន្នន័យនាយទាហាន (Military Personnel Management System)
   Modern Mobile & Desktop UI Design System
   ========================================================================== */

:root {
    --navy-header: #0f2537;
    --navy-dark: #07131e;
    --navy-primary: #003366;
    --navy-light: #1e3a5f;
    --gold-accent: #d4af37;
    --gold-bright: #f59e0b;
    --win-titlebar: #0c1825;
    --win-bg: #f0f4f8;
    --card-bg: #ffffff;
    --border-color: #cbd5e1;
    --border-focus: #0284c7;

    --btn-add: linear-gradient(135deg, #059669 0%, #047857 100%);
    --btn-update: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    --btn-delete: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    --btn-clear: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    --btn-exit: linear-gradient(135deg, #475569 0%, #334155 100%);
    --btn-search: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);

    --row-selected: #0284c7;
    --font-main: 'Kantumruy Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 12px rgba(15, 37, 55, 0.08), 0 2px 4px rgba(15, 37, 55, 0.04);
    --shadow-lg: 0 10px 25px rgba(15, 37, 55, 0.15);
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: var(--font-main);
    -webkit-tap-highlight-color: transparent;
}

body.desktop-app-body {
    background: #e2e8f0;
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 20px 20px;
    color: #1e293b;
    font-size: 13.5px;
    line-height: 1.5;
    padding: 12px;
    min-height: 100vh;
}

.window-container {
    background: var(--win-bg);
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
    max-width: 1480px;
    margin: 0 auto;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 24px);
    transition: all 0.3s ease;
}

.window-titlebar {
    background: linear-gradient(135deg, #091420 0%, #0f2537 100%);
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 600;
    border-bottom: 2px solid var(--gold-accent);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.titlebar-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.app-icon {
    color: var(--gold-bright);
    font-size: 18px;
    filter: drop-shadow(0 0 4px rgba(245, 158, 11, 0.4));
}

.app-title-text {
    letter-spacing: 0.2px;
}

.titlebar-right {
    display: flex;
    align-items: center;
    gap: 6px;
}

.win-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #cbd5e1;
    width: 32px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.win-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    transform: translateY(-1px);
}

.win-btn.win-close:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}

.window-content {
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    flex: 1;
}

.input-form-card {
    background: var(--card-bg);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 14px 14px 14px;
    box-shadow: var(--shadow-sm);
    position: relative;
}

.form-legend {
    background: linear-gradient(90deg, #003366 0%, #004d99 100%);
    color: #ffffff;
    font-weight: 600;
    font-size: 13.5px;
    padding: 4px 14px;
    border-radius: 20px;
    margin-left: 10px;
    box-shadow: 0 2px 6px rgba(0, 51, 102, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.form-grid-layout {
    display: grid;
    grid-template-columns: 1fr 1fr 280px;
    gap: 16px;
    margin-top: 4px;
}

.form-col {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-row {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 36px;
}

.field-label {
    width: 140px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    text-align: right;
    padding-right: 4px;
}

.field-input-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    position: relative;
}

.form-control {
    width: 100%;
    height: 36px;
    padding: 6px 10px;
    font-size: 13.5px;
    color: #0f172a;
    background-color: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    transition: all 0.2s ease-in-out;
    outline: none;
}

.form-control:hover {
    border-color: #94a3b8;
    background-color: #ffffff;
}

.form-control:focus {
    background-color: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.form-control.select-ctrl {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 32px;
    cursor: pointer;
}

.form-control[readonly] {
    background-color: #e2e8f0;
    color: #475569;
    font-weight: 700;
    cursor: not-allowed;
}

.dob-wrap {
    gap: 6px;
}

.age-badge {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-weight: 700;
    font-size: 12px;
    padding: 0 10px;
    height: 36px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.col-3 {
    border-left: 1px dashed #cbd5e1;
    padding-left: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.photo-box-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px;
    text-align: center;
    box-shadow: var(--shadow-sm);
}

.photo-title {
    font-size: 12px;
    font-weight: 700;
    color: #003366;
    margin-bottom: 6px;
}

.photo-frame {
    width: 110px;
    height: 130px;
    margin: 0 auto;
    border: 2px dashed #94a3b8;
    border-radius: 6px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    transition: all 0.2s ease;
}

.photo-frame:hover {
    border-color: #0284c7;
    background: #f0f9ff;
}

.photo-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-overlay-btn {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: rgba(15, 37, 55, 0.85);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
}

.photo-upload-input {
    display: none;
}

.matrix-grid {
    display: grid;
    gap: 4px;
}

.grid-2x2 {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 32px 32px;
}

.grid-2x3 {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 32px 32px 32px;
}

.matrix-cell {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
}

.cell-label {
    color: #64748b;
    font-weight: 500;
}

.cell-val {
    font-weight: 700;
    color: #0f172a;
}

.action-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    box-shadow: var(--shadow-sm);
}

.action-btn-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.act-btn {
    height: 38px;
    padding: 0 16px;
    border: none;
    border-radius: 6px;
    color: #ffffff;
    font-weight: 600;
    font-size: 13.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.act-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.act-btn:active {
    transform: translateY(0);
}

.btn-add { background: var(--btn-add); }
.btn-update { background: var(--btn-update); }
.btn-delete { background: var(--btn-delete); }
.btn-clear { background: var(--btn-clear); }
.btn-exit { background: var(--btn-exit); }

.search-fieldset {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px;
    box-shadow: var(--shadow-sm);
}

.search-toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.search-item {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 160px;
}

.search-control {
    height: 36px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    padding: 0 10px;
    font-size: 13px;
}

.btn-search {
    background: var(--btn-search);
    color: #ffffff;
    height: 36px;
    padding: 0 18px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.btn-search:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.grid-table-wrap {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 400px;
    box-shadow: var(--shadow-sm);
}

.desktop-grid-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    text-align: left;
    white-space: nowrap;
}

.desktop-grid-table th {
    background: #0f2537;
    color: #f59e0b;
    font-weight: 600;
    padding: 10px 12px;
    position: sticky;
    top: 0;
    z-index: 2;
    border-bottom: 2px solid var(--gold-accent);
}

.desktop-grid-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
}

.desktop-grid-table tr:nth-child(even) {
    background-color: #f8fafc;
}

.desktop-grid-table tr:hover {
    background-color: #e0f2fe;
    cursor: pointer;
}

.desktop-grid-table tr.selected {
    background-color: var(--row-selected) !important;
    color: #ffffff !important;
}

.desktop-grid-table tr.selected td {
    color: #ffffff !important;
}

.app-statusbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #0f2537;
    color: #cbd5e1;
    border-top: 1px solid #1e293b;
    padding: 8px 14px;
    font-size: 12px;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.statusbar-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.statusbar-credit {
    font-weight: 600;
    color: var(--gold-bright);
}

.d-none { display: none !important; }
.text-center { text-align: center; }

@media screen and (max-width: 1024px) {
    .form-grid-layout {
        grid-template-columns: 1fr 1fr;
    }
    .col-3 {
        grid-column: span 2;
        border-left: none;
        border-top: 1px dashed #cbd5e1;
        padding-left: 0;
        padding-top: 12px;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
    }
}

@media screen and (max-width: 768px) {
    body.desktop-app-body {
        padding: 0;
        background: #f0f4f8;
    }

    .window-container {
        border: none;
        border-radius: 0;
        box-shadow: none;
        min-height: 100vh;
    }

    .window-titlebar {
        padding: 12px 14px;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .app-title-text {
        font-size: 13px;
        font-weight: 700;
    }

    .titlebar-right .win-setting,
    .titlebar-right .win-min,
    .titlebar-right .win-max {
        display: none;
    }

    .window-content {
        padding: 10px;
        gap: 12px;
    }

    .input-form-card {
        padding: 14px 10px 10px 10px;
        border-radius: 12px;
    }

    .form-legend {
        font-size: 13px;
        padding: 4px 12px;
    }

    .form-grid-layout {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .col-3 {
        grid-column: span 1;
        border-top: 1px dashed #cbd5e1;
        padding-left: 0;
        padding-top: 12px;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .form-row {
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
        min-height: auto;
    }

    .field-label {
        width: 100%;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        color: #0f2537;
    }

    .field-input-wrap {
        width: 100%;
    }

    .form-control {
        height: 42px;
        font-size: 14px;
        border-radius: 8px;
    }

    .age-badge {
        height: 42px;
        font-size: 13px;
        border-radius: 8px;
    }

    .action-toolbar {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
        padding: 10px;
        border-radius: 10px;
    }

    .action-btn-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        width: 100%;
    }

    .act-btn {
        height: 44px;
        font-size: 13.5px;
        justify-content: center;
        border-radius: 8px;
    }

    .action-btn-exit-wrap {
        width: 100%;
    }

    .btn-exit {
        width: 100%;
        height: 44px;
        justify-content: center;
        border-radius: 8px;
    }

    .search-fieldset {
        padding: 10px;
        border-radius: 10px;
    }

    .search-toolbar {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }

    .search-item {
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
        min-width: 100%;
    }

    .search-control {
        height: 42px;
        font-size: 14px;
        border-radius: 8px;
    }

    .btn-search {
        height: 44px;
        font-size: 14px;
        justify-content: center;
        border-radius: 8px;
    }

    .grid-table-wrap {
        max-height: 350px;
        border-radius: 8px;
        -webkit-overflow-scrolling: touch;
    }

    .desktop-grid-table th, 
    .desktop-grid-table td {
        padding: 10px 10px;
        font-size: 12px;
    }

    .app-statusbar {
        flex-direction: column;
        gap: 4px;
        text-align: center;
        padding: 10px;
        border-radius: 0;
    }
}
    </style>
</head>
<body class="desktop-app-body">

    <!-- Desktop Window Container -->
    <div class="window-container">

        <!-- Titlebar Header -->
        <div class="window-titlebar">
            <div class="titlebar-left">
                <i class="fas fa-shield-halved app-icon"></i>
                <span class="app-title-text">ប្រព័ន្ធគ្រប់គ្រងទិន្នន័យនាយទាហាន (Military Personnel Management System)</span>
            </div>
            <div class="titlebar-right">
                <button class="win-btn win-setting" title="ការកំណត់"><i class="fas fa-cog"></i></button>
                <button class="win-btn win-min" title="បង្រួម"><i class="fas fa-minus"></i></button>
                <button class="win-btn win-max" title="ពង្រីកពេញអេក្រង់"><i class="far fa-square"></i></button>
                <button class="win-btn win-close" title="បិទ"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- Main Desktop Content Area -->
        <div class="window-content">

            <!-- Input Form Fieldset Box -->
            <fieldset class="input-form-card">
                <legend class="form-legend">បញ្ចូលព័ត៌មាន (Input Form)</legend>

                <div class="form-grid-layout">
                    
                    <!-- Column 1: Identification & Ranks -->
                    <div class="form-col col-1">
                        <div class="form-row">
                            <label class="field-label" for="id">ល.រ:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="id" class="form-control text-center" readonly value="525">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="manual_id">ល.រ (វាយដោយដៃ):</label>
                            <div class="field-input-wrap">
                                <input type="text" id="manual_id" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="rank">ឋានន្តរស័ក្តិ:</label>
                            <div class="field-input-wrap">
                                <select id="rank" class="form-control select-ctrl">
                                    <option value="ពលលេខា">ពលលេខា</option>
                                    <option value="ពលទោ">ពលទោ</option>
                                    <option value="ពលឯក">ពលឯក</option>
                                    <option value="នាយបាល">នាយបាល</option>
                                    <option value="ព្រិន្ទបាលទោ">ព្រិន្ទបាលទោ</option>
                                    <option value="ព្រិន្ទបាលឯក">ព្រិន្ទបាលឯក</option>
                                    <option value="នាយចំណង់">នាយចំណង់</option>
                                    <option value="អនុសេនីយ៍ត្រី">អនុសេនីយ៍ត្រី</option>
                                    <option value="អនុសេនីយ៍ទោ">អនុសេនីយ៍ទោ</option>
                                    <option value="អនុសេនីយ៍ឯក">អនុសេនីយ៍ឯក</option>
                                    <option value="វរសេនីយ៍ត្រី">វរសេនីយ៍ត្រី</option>
                                    <option value="វរសេនីយ៍ទោ">វរសេនីយ៍ទោ</option>
                                    <option value="វរសេនីយ៍ឯក">វរសេនីយ៍ឯក</option>
                                    <option value="ឧត្តមសេនីយ៍ត្រី">ឧត្តមសេនីយ៍ត្រី</option>
                                    <option value="ឧត្តមសេនីយ៍ទោ">ឧត្តមសេនីយ៍ទោ</option>
                                    <option value="ឧត្តមសេនីយ៍ឯក">ឧត្តមសេនីយ៍ឯក</option>
                                    <option value="នាយឧត្តមសេនីយ៍">នាយឧត្តមសេនីយ៍</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="surname">គោត្តនាម:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="surname" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="given_name">នាម:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="given_name" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="gender">ភេទ:</label>
                            <div class="field-input-wrap">
                                <select id="gender" class="form-control select-ctrl">
                                    <option value="ប">ប</option>
                                    <option value="ស">ស</option>
                                    <option value="ប្រុស">ប្រុស</option>
                                    <option value="ស្រី">ស្រី</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="id_card">អត្តលេខ:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="id_card" class="form-control" placeholder="012345 (៦ ខ្ទង់)">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="position">មុខតំណែង:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="position" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="unit_group">កងឯកភាព:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="unit_group" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="unit">អង្គភាព:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="unit" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="rank_date">ថ្ងៃខែឆ្នាំឡើងស័ក្តិ:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="rank_date" class="form-control date-input" placeholder="DD/MM/YYYY">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="position_date">ថ្ងៃខែឆ្នាំឡើងមុខងារ:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="position_date" class="form-control date-input" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Dates, Education, POB, Address -->
                    <div class="form-col col-2">
                        <div class="form-row">
                            <label class="field-label" for="dob">ថ្ងៃខែឆ្នាំកំណើត:</label>
                            <div class="field-input-wrap dob-wrap">
                                <input type="text" id="dob" class="form-control date-input" placeholder="DD/MM/YYYY">
                                <span class="age-badge" id="ageDisplay">អាយុ</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="enlistment_date">ថ្ងៃខែឆ្នាំចូលទ័ព:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="enlistment_date" class="form-control date-input" placeholder="DD/MM/YYYY">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="education_level">កម្រិតវប្បធម៌:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="education_level" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="study_local">រៀនក្នុងប្រទេស:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="study_local" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="study_abroad">រៀនក្រៅប្រទេស:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="study_abroad" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="children_count">ចំនួនកូន:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="children_count" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="black_card_expiry">ថ្ងៃខែផុតកំណត់កាតស៊ីវិល:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="black_card_expiry" class="form-control date-input" placeholder="DD/MM/YYYY ឬ អចិន្ត្រៃយ៍">
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="blue_card_expiry">ថ្ងៃខែផុតកំណត់កាតទាហាន:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="blue_card_expiry" class="form-control date-input" placeholder="DD/MM/YYYY">
                            </div>
                        </div>

                        <!-- ស្រុកកំណើត Matrix (2 columns x 2 rows) -->
                        <div class="form-row multi-input-row">
                            <label class="field-label">ស្រុកកំណើត:</label>
                            <div class="field-input-wrap matrix-wrap">
                                <div class="matrix-grid grid-2x2">
                                    <input type="text" id="pob_village" class="form-control" placeholder="ភូមិ">
                                    <input type="text" id="pob_commune" class="form-control" placeholder="ឃុំ/សង្កាត់">
                                    <input type="text" id="pob_district" class="form-control" placeholder="ស្រុក/ខណ្ឌ">
                                    <input type="text" id="pob_province" class="form-control" placeholder="ខេត្ត/ក្រុង">
                                </div>
                            </div>
                        </div>

                        <!-- ទីលំនៅបច្ចុប្បន្ន Matrix (2 columns x 3 rows) -->
                        <div class="form-row multi-input-row">
                            <label class="field-label">ទីលំនៅបច្ចុប្បន្ន:</label>
                            <div class="field-input-wrap matrix-wrap">
                                <div class="matrix-grid grid-2x3">
                                    <input type="text" id="addr_house" class="form-control" placeholder="ផ្ទះលេខ">
                                    <input type="text" id="addr_group" class="form-control" placeholder="ក្រុមទី">
                                    <input type="text" id="addr_village" class="form-control" placeholder="ភូមិ">
                                    <input type="text" id="addr_commune" class="form-control" placeholder="ឃុំ/សង្កាត់">
                                    <input type="text" id="addr_district" class="form-control" placeholder="ស្រុក/ខណ្ឌ">
                                    <input type="text" id="addr_province" class="form-control" placeholder="ខេត្ត/ក្រុង">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="field-label" for="notes">ផ្សេងៗ:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="notes" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Phone, Photos, Family Info -->
                    <div class="form-col col-3">
                        <div class="form-row phone-row">
                            <label class="field-label" for="phone">លេខទូរស័ព្ទ:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="phone" class="form-control font-bold" placeholder="012... / 098...">
                            </div>
                        </div>

                        <!-- Photo Section 1: Officer Photo 4x6 -->
                        <div class="photo-section">
                            <div class="photo-title">រូបថតសាមីខ្លួន (4x6)</div>
                            <div class="photo-frame-box dashed-frame" id="photoFrame">
                                <img src="" id="photoImg" alt="" class="d-none">
                                <span class="dashed-label" id="photoDashedLabel">រូបថត 4x6</span>
                            </div>
                            <div class="photo-action-btn-row">
                                <button type="button" class="btn-pic btn-pic-select" onclick="document.getElementById('photoFileInput').click()">ជ្រើសរើស</button>
                                <button type="button" class="btn-pic btn-pic-delete" onclick="removePhoto('photoImg', 'photoFileInput', 'photoDashedLabel')">លុប</button>
                                <input type="file" id="photoFileInput" accept="image/*" class="d-none" onchange="previewPersonalImage(this)">
                            </div>
                        </div>

                        <!-- Photo Section 2: Family Photo 4x6 -->
                        <div class="photo-section family-photo-sec">
                            <div class="photo-title">រូបថតគ្រួសារ</div>
                            <div class="photo-frame-box dashed-frame" id="familyPhotoFrame">
                                <img src="" id="familyPhotoImg" alt="" class="d-none">
                                <span class="dashed-label" id="familyDashedLabel">រូបថតគ្រួសារ 4x6</span>
                            </div>
                            <div class="photo-action-btn-row">
                                <button type="button" class="btn-pic btn-pic-select" onclick="document.getElementById('familyPhotoFileInput').click()">ជ្រើសរើស</button>
                                <button type="button" class="btn-pic btn-pic-delete" onclick="removePhoto('familyPhotoImg', 'familyPhotoFileInput', 'familyDashedLabel')">លុប</button>
                                <input type="file" id="familyPhotoFileInput" accept="image/*" class="d-none" onchange="previewFamilyImage(this)">
                            </div>
                        </div>

                        <div class="form-row family-name-row">
                            <label class="field-label" for="family_name">ឈ្មោះគ្រួសារ:</label>
                            <div class="field-input-wrap">
                                <input type="text" id="family_name" class="form-control" placeholder="នាមត្រកូល...">
                            </div>
                        </div>
                    </div>

                </div>
            </fieldset>

            <!-- Action Command Buttons Toolbar -->
            <div class="action-toolbar">
                <div class="action-btn-group">
                    <button type="button" class="act-btn btn-add-new" id="btnAdd"><i class="fas fa-plus"></i> បញ្ចូលថ្មី (Add New)</button>
                    <button type="button" class="act-btn btn-update" id="btnUpdate"><i class="fas fa-edit"></i> កែប្រែទិន្នន័យ (Update)</button>
                    <button type="button" class="act-btn btn-delete" id="btnDelete"><i class="fas fa-trash-alt"></i> លុបទិន្នន័យ (Delete)</button>
                    <button type="button" class="act-btn btn-clear" id="btnClear"><i class="fas fa-sync-alt"></i> ឡូកទិន្នន័យ (Clear)</button>
                </div>
                <div class="action-btn-exit-wrap">
                    <button type="button" class="act-btn btn-exit" id="btnExit" onclick="window.close()"><i class="fas fa-sign-out-alt"></i> ចាកចេញ (Exit)</button>
                </div>
            </div>

            <!-- Search Filter Bar -->
            <div class="search-toolbar">
                <div class="search-item search-id-group">
                    <label for="searchIdInput" class="search-label">ស្វែងរកតាមអត្តលេខ (Search ID):</label>
                    <input type="text" id="searchIdInput" class="search-control" placeholder="012345 (៦ ខ្ទង់)">
                    <button type="button" class="act-btn btn-search" id="btnSearchId"><i class="fas fa-search"></i> ស្វែងរក (Search ID)</button>
                </div>

                <div class="search-item search-name-group">
                    <label for="searchNameInput" class="search-label">ស្វែងរកតាមឈ្មោះ (Search Name):</label>
                    <input type="text" id="searchNameInput" class="search-control" placeholder="">
                    <button type="button" class="act-btn btn-search" id="btnSearchName"><i class="fas fa-search"></i> ស្វែងរកតាមឈ្មោះ (Search Name)</button>
                </div>
            </div>

            <!-- Main Data Table Container -->
            <div class="grid-table-card">
                <div class="grid-table-wrap">
                    <table class="desktop-grid-table" id="personnelTable">
                        <thead>
                            <tr>
                                <th>ល.រ</th>
                                <th>ល.រ (វាយដោយដៃ)</th>
                                <th>ឋានន្តរស័ក្តិ</th>
                                <th>គោត្តនាម</th>
                                <th>នាម</th>
                                <th>ភេទ</th>
                                <th>អត្តលេខ</th>
                                <th>មុខតំណែង</th>
                                <th>កងឯកភាព</th>
                                <th>អង្គភាព</th>
                                <th>ថ្ងៃខែឆ្នាំកំណើត</th>
                                <th>ថ្ងៃខែឆ្នាំចូលទ័ព</th>
                                <th>ថ្ងៃខែឆ្នាំឡើងស័ក្តិ</th>
                            </tr>
                        </thead>
                        <tbody id="personnelTableBody">
                            <!-- Loaded dynamically via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- App Footer Status Bar -->
            <div class="app-statusbar">
                <div class="statusbar-info">
                    <i class="fas fa-info-circle"></i> <span id="statusMessage">ទិន្នន័យត្រូវបានផ្ទុកជោគជ័យ</span>
                </div>
                <div class="statusbar-credit">
                    អ្នកបង្កើតកម្មវិធី៖ ម៉ាស់ សាយ៉ូ (ឆ្នាំ ២០២៦)
                </div>
            </div>

        </div>
    </div>

    <!-- JavaScript Bundle -->
    <script src="assets/js/app.js"></script>
    <script>
/**
 * កម្មវិធីគ្រប់គ្រងទិន្នន័យនាយទាហាន (Military Personnel Management System)
 * JavaScript Interactive Application Engine
 */

document.addEventListener('DOMContentLoaded', () => {
    let personnelData = [];
    let selectedPersonnelId = null;

    const formFields = [
        'id', 'manual_id', 'rank', 'surname', 'given_name', 'gender', 'id_card',
        'position', 'unit_group', 'unit', 'rank_date', 'position_date',
        'dob', 'enlistment_date', 'education_level', 'study_local', 'study_abroad',
        'children_count', 'black_card_expiry', 'blue_card_expiry',
        'pob_village', 'pob_commune', 'pob_district', 'pob_province',
        'addr_house', 'addr_group', 'addr_village', 'addr_commune', 'addr_district', 'addr_province',
        'notes', 'phone', 'family_name'
    ];

    const tableBody = document.getElementById('personnelTableBody');
    const dobInput = document.getElementById('dob');
    const ageDisplay = document.getElementById('ageDisplay');
    const statusMessage = document.getElementById('statusMessage');

    const photoImg = document.getElementById('photoImg');
    const photoDashedLabel = document.getElementById('photoDashedLabel');
    const familyPhotoImg = document.getElementById('familyPhotoImg');
    const familyDashedLabel = document.getElementById('familyDashedLabel');

    if (photoImg) {
        photoImg.onerror = function() {
            this.src = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='105' height='130' viewBox='0 0 105 130'><rect width='105' height='130' fill='%231e293b'/><rect x='4' y='4' width='97' height='122' fill='%230f2537' stroke='%23d4af37' stroke-width='1.5'/><circle cx='52.5' cy='46' r='22' fill='%23d4af37'/><path d='M22,108 C22,78 83,78 83,108 Z' fill='%23d4af37'/><text x='52.5' y='120' font-family='sans-serif' font-size='10' font-weight='bold' fill='%23ffffff' text-anchor='middle'>4x6</text></svg>";
        };
    }

    if (familyPhotoImg) {
        familyPhotoImg.onerror = function() {
            this.classList.add('d-none');
            if (familyDashedLabel) familyDashedLabel.classList.remove('d-none');
        };
    }

    const btnAdd = document.getElementById('btnAdd');
    const btnUpdate = document.getElementById('btnUpdate');
    const btnDelete = document.getElementById('btnDelete');
    const btnClear = document.getElementById('btnClear');
    const btnSearchId = document.getElementById('btnSearchId');
    const btnSearchName = document.getElementById('btnSearchName');
    const searchIdInput = document.getElementById('searchIdInput');
    const searchNameInput = document.getElementById('searchNameInput');

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./sw.js')
            .then(reg => console.log('[PWA] ServiceWorker Registered:', reg.scope))
            .catch(err => console.log('[PWA] ServiceWorker Failed:', err));
    }

    loadPersonnel();

    const blackCardInput = document.getElementById('black_card_expiry');
    const blueCardInput = document.getElementById('blue_card_expiry');

    if (dobInput) {
        dobInput.addEventListener('input', updateAgeDisplay);
        dobInput.addEventListener('change', updateAgeDisplay);
    }

    [blackCardInput, blueCardInput].forEach(inp => {
        if (inp) {
            inp.addEventListener('input', checkExpiryDates);
            inp.addEventListener('change', checkExpiryDates);
        }
    });

    if (btnAdd) btnAdd.addEventListener('click', handleAdd);
    if (btnUpdate) btnUpdate.addEventListener('click', handleUpdate);
    if (btnDelete) btnDelete.addEventListener('click', handleDelete);
    if (btnClear) btnClear.addEventListener('click', clearForm);

    if (btnSearchId) btnSearchId.addEventListener('click', () => loadPersonnel({ search_id: searchIdInput.value.trim() }));
    if (btnSearchName) btnSearchName.addEventListener('click', () => loadPersonnel({ search_name: searchNameInput.value.trim() }));

    if (searchIdInput) {
        searchIdInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') loadPersonnel({ search_id: searchIdInput.value.trim() });
        });
    }

    if (searchNameInput) {
        searchNameInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') loadPersonnel({ search_name: searchNameInput.value.trim() });
        });
    }

    async function loadPersonnel(filters = {}) {
        setStatus('កំពុងផ្ទុកទិន្នន័យ...');
        const params = new URLSearchParams({ action: 'fetch_all', ...filters });

        try {
            const res = await fetch(`api.php?${params.toString()}`);
            const result = await res.json();

            if (result.success) {
                personnelData = result.data || [];
                try {
                    localStorage.setItem('military_personnel_cache', JSON.stringify(personnelData));
                } catch (e) {}
                renderTable(personnelData);
                setStatus(`ទិន្នន័យត្រូវបានផ្ទុកជោគជ័យ សរុប ${personnelData.length} នាក់`);
            } else {
                setStatus(`មានបញ្ហា: ${result.message}`);
            }
        } catch (err) {
            console.log('Offline/Local fallback triggered');
            const cached = localStorage.getItem('military_personnel_cache');
            if (cached) {
                try {
                    personnelData = JSON.parse(cached);
                } catch(e) {}
            }
            renderTable(personnelData);
            setStatus(`ដំណើរការលើទូរស័ព្ទ (Mobile Offline Mode) - សរុប ${personnelData.length} នាក់`);
        }
    }

    function renderTable(data) {
        tableBody.innerHTML = '';

        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="14" class="text-center" style="padding: 20px; color: #64748b;">
                        ពុំមានទិន្នន័យនាយទាហានឡើយ
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach((p, idx) => {
            const tr = document.createElement('tr');
            tr.dataset.id = p.id;

            if (selectedPersonnelId && String(p.id) === String(selectedPersonnelId)) {
                tr.classList.add('selected');
            }

            tr.innerHTML = `
                <td>${p.id || (idx + 1)}</td>
                <td>${p.manual_id || ''}</td>
                <td>${p.rank || ''}</td>
                <td>${p.surname || ''}</td>
                <td>${p.given_name || p.name_khmer || ''}</td>
                <td>${p.gender || ''}</td>
                <td>${p.id_card || ''}</td>
                <td>${p.position || ''}</td>
                <td>${p.unit_group || ''}</td>
                <td>${p.unit || ''}</td>
                <td>${formatDisplayDate(p.dob)}</td>
                <td>${formatDisplayDate(p.enlistment_date)}</td>
                <td>${formatDisplayDate(p.rank_date)}</td>
            `;

            tr.addEventListener('click', () => selectRow(p, tr));
            tableBody.appendChild(tr);
        });

        if (!selectedPersonnelId && data.length > 0) {
            const firstRow = tableBody.querySelector('tr');
            if (firstRow) selectRow(data[0], firstRow);
        }
    }

    function selectRow(personnel, trElement) {
        selectedPersonnelId = personnel.id;

        const allRows = tableBody.querySelectorAll('tr');
        allRows.forEach(r => r.classList.remove('selected'));
        if (trElement) trElement.classList.add('selected');

        formFields.forEach(field => {
            const el = document.getElementById(field);
            if (!el) return;

            if (el.type === 'checkbox') {
                el.checked = Boolean(personnel[field]);
            } else {
                let val = personnel[field] || '';
                if (['dob', 'enlistment_date', 'rank_date', 'position_date', 'black_card_expiry', 'blue_card_expiry'].includes(field)) {
                    val = formatDisplayDate(val);
                }
                el.value = val;
            }
        });

        const DEFAULT_AVATAR = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='105' height='130' viewBox='0 0 105 130'><rect width='105' height='130' fill='%231e293b'/><rect x='4' y='4' width='97' height='122' fill='%230f2537' stroke='%23d4af37' stroke-width='1.5'/><circle cx='52.5' cy='46' r='22' fill='%23d4af37'/><path d='M22,108 C22,78 83,78 83,108 Z' fill='%23d4af37'/><text x='52.5' y='120' font-family='sans-serif' font-size='10' font-weight='bold' fill='%23ffffff' text-anchor='middle'>4x6</text></svg>";

        if (personnel.photo) {
            photoImg.src = personnel.photo;
            photoImg.classList.remove('d-none');
        } else {
            photoImg.src = DEFAULT_AVATAR;
            photoImg.classList.remove('d-none');
        }

        if (personnel.family_photo) {
            familyPhotoImg.src = personnel.family_photo;
            familyPhotoImg.classList.remove('d-none');
            if (familyDashedLabel) familyDashedLabel.classList.add('d-none');
        } else {
            familyPhotoImg.src = '';
            familyPhotoImg.classList.add('d-none');
            if (familyDashedLabel) familyDashedLabel.classList.remove('d-none');
        }

        updateAgeDisplay();
        checkExpiryDates();
        setStatus(`បានជ្រើសរើស៖ ${personnel.surname || ''} ${personnel.given_name || personnel.name_khmer || ''} (អត្តលេខ: ${personnel.id_card || '-'})`);
    }

    async function handleAdd() {
        const payload = collectFormData();
        if (!payload.surname && !payload.given_name && !payload.name_khmer) {
            alert('សូមបញ្ចូល "គោត្តនាម" ឬ "នាម" របស់នាយទាហាន!');
            return;
        }

        setStatus('កំពុងបន្ថែមទិន្នន័យថ្មី...');
        try {
            const res = await fetch('api.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                loadPersonnel();
            } else {
                alert('បរាជ័យ៖ ' + result.message);
            }
        } catch (err) {
            alert('មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ API');
        }
    }

    async function handleUpdate() {
        if (!selectedPersonnelId) {
            alert('សូមជ្រើសរើសជួរទិន្នន័យនាយទាហានក្នុងតារាងជាមុនសិន!');
            return;
        }

        const payload = collectFormData();
        payload.id = selectedPersonnelId;

        setStatus('កំពុងកែប្រែទិន្នន័យ...');
        try {
            const res = await fetch('api.php?action=edit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                loadPersonnel();
            } else {
                alert('បរាជ័យ៖ ' + result.message);
            }
        } catch (err) {
            alert('មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ API');
        }
    }

    async function handleDelete() {
        if (!selectedPersonnelId) {
            alert('សូមជ្រើសរើសជួរទិន្នន័យនាយទាហានដែលត្រូវលុបជាមុនសិន!');
            return;
        }

        if (!confirm('តើអ្នកប្រាកដជាចង់លុបទិន្នន័យនាយទាហាននេះមែនទេ?')) return;

        setStatus('កំពុងលុបទិន្នន័យ...');
        try {
            const res = await fetch('api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: selectedPersonnelId })
            });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                selectedPersonnelId = null;
                clearForm();
                loadPersonnel();
            } else {
                alert('បរាជ័យ៖ ' + result.message);
            }
        } catch (err) {
            alert('មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ API');
        }
    }

    function clearForm() {
        selectedPersonnelId = null;
        formFields.forEach(field => {
            const el = document.getElementById(field);
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = false;
            } else if (field === 'children_count') {
                el.value = '0';
            } else if (field === 'gender') {
                el.value = 'ស';
            } else if (field === 'rank') {
                el.value = 'ព្រិន្ទបាលឯក';
            } else {
                el.value = '';
            }
        });

        const DEFAULT_AVATAR = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='105' height='130' viewBox='0 0 105 130'><rect width='105' height='130' fill='%231e293b'/><rect x='4' y='4' width='97' height='122' fill='%230f2537' stroke='%23d4af37' stroke-width='1.5'/><circle cx='52.5' cy='46' r='22' fill='%23d4af37'/><path d='M22,108 C22,78 83,78 83,108 Z' fill='%23d4af37'/><text x='52.5' y='120' font-family='sans-serif' font-size='10' font-weight='bold' fill='%23ffffff' text-anchor='middle'>4x6</text></svg>";
        if (photoImg) {
            photoImg.src = DEFAULT_AVATAR;
            photoImg.classList.remove('d-none');
        }

        if (familyPhotoImg) {
            familyPhotoImg.src = '';
            familyPhotoImg.classList.add('d-none');
        }
        if (familyDashedLabel) familyDashedLabel.classList.remove('d-none');

        const allRows = tableBody.querySelectorAll('tr');
        allRows.forEach(r => r.classList.remove('selected'));

        if (ageDisplay) ageDisplay.innerText = '-- ឆ្នាំ';
        checkExpiryDates();
        setStatus('បានសម្អាតទម្រង់បញ្ចូល');
    }

    function collectFormData() {
        const payload = {};
        formFields.forEach(field => {
            const el = document.getElementById(field);
            if (!el) return;
            if (el.type === 'checkbox') {
                payload[field] = el.checked ? 1 : 0;
            } else {
                let val = el.value.trim();
                if (['dob', 'enlistment_date', 'rank_date', 'position_date', 'black_card_expiry', 'blue_card_expiry'].includes(field)) {
                    val = parseIsoDate(val);
                }
                payload[field] = val;
            }
        });

        payload.name_khmer = `${payload.surname || ''} ${payload.given_name || ''}`.trim();
        payload.photo = photoImg.src.startsWith('data:image') ? photoImg.src : '';
        payload.family_photo = (familyPhotoImg && !familyPhotoImg.classList.contains('d-none')) ? familyPhotoImg.src : '';
        return payload;
    }

    function updateAgeDisplay() {
        if (!dobInput || !ageDisplay) return;
        const val = dobInput.value.trim();
        if (!val) {
            ageDisplay.innerText = '-- ឆ្នាំ';
            return;
        }

        let birthYear = null;
        if (val.includes('/')) {
            const parts = val.split('/');
            if (parts.length === 3) birthYear = parseInt(parts[2], 10);
        } else if (val.includes('-')) {
            const parts = val.split('-');
            if (parts.length === 3) birthYear = parseInt(parts[0], 10);
        }

        if (birthYear && !isNaN(birthYear)) {
            const currentYear = new Date().getFullYear();
            const age = currentYear - birthYear;
            ageDisplay.innerText = `${age} ឆ្នាំ`;
        } else {
            ageDisplay.innerText = '-- ឆ្នាំ';
        }
    }

    function formatDisplayDate(dateStr) {
        if (!dateStr) return '';
        if (dateStr.includes('/')) return dateStr;
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return `${parts[2].padStart(2, '0')}/${parts[1].padStart(2, '0')}/${parts[0]}`;
        }
        return dateStr;
    }

    function parseIsoDate(displayDate) {
        if (!displayDate) return null;
        if (displayDate.includes('-')) return displayDate;
        const parts = displayDate.split('/');
        if (parts.length === 3) {
            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }
        return displayDate;
    }

    function setStatus(msg) {
        if (statusMessage) statusMessage.innerText = msg;
    }

    function checkExpiryDates() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        [blackCardInput, blueCardInput].forEach(input => {
            if (!input) return;
            const val = input.value.trim();
            if (!val || val.includes('អចិន្ត្រៃយ៍')) {
                input.classList.remove('input-expired');
                return;
            }

            const parsedDate = parseDateToObj(val);
            if (parsedDate && parsedDate < today) {
                input.classList.add('input-expired');
            } else {
                input.classList.remove('input-expired');
            }
        });
    }

    function parseDateToObj(dateStr) {
        if (!dateStr) return null;
        if (dateStr.includes('/')) {
            const parts = dateStr.split('/');
            if (parts.length === 3) {
                const d = parseInt(parts[0], 10);
                const m = parseInt(parts[1], 10) - 1;
                const y = parseInt(parts[2], 10);
                return new Date(y, m, d);
            }
        } else if (dateStr.includes('-')) {
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                } else {
                    return new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10));
                }
            }
        }
        return null;
    }
});
    </script>
</body>
</html>
