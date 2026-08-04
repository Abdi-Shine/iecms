<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IECMS Brand Guidelines & Design System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-neutral-50 text-neutral-800">

    <!-- Header -->
    <header class="bg-primary-900 text-white py-16 px-8 text-center">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-2xl bg-gold-400 flex items-center justify-center">
                    <i class="fas fa-balance-scale text-3xl text-white"></i>
                </div>
            </div>
            <h1 class="text-5xl font-bold mb-3">IECMS Design System</h1>
            <p class="text-primary-200 text-lg">Brand Guidelines & Frontend Component Library</p>
            <p class="text-primary-300 text-sm mt-2">Integrated Electronic Case Management System — Supreme Court of Somalia</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-8 py-12 space-y-20">

        <!-- ═══ 1. BRAND IDENTITY ════════════════════════════════════════ -->
        <section id="brand-identity">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                1. Brand Identity
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="card card-body">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                        <i class="fas fa-bullseye text-primary-600"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2">Mission</h3>
                    <p class="text-sm text-neutral-600 leading-relaxed">To deliver a secure, unified digital platform that modernises judicial case management, ensuring transparency, efficiency, and equal access to justice across Somalia.</p>
                </div>
                <div class="card card-body">
                    <div class="w-10 h-10 rounded-lg bg-gold-100 flex items-center justify-center mb-4">
                        <i class="fas fa-eye text-gold-600"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2">Vision</h3>
                    <p class="text-sm text-neutral-600 leading-relaxed">A fully paperless, data-driven judiciary that upholds the rule of law through technology — trusted by citizens, legal professionals, and institutions alike.</p>
                </div>
                <div class="card card-body">
                    <div class="w-10 h-10 rounded-lg bg-success-100 flex items-center justify-center mb-4">
                        <i class="fas fa-comments text-success-600"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2">Tone of Voice</h3>
                    <p class="text-sm text-neutral-600 leading-relaxed">Formal, clear, and authoritative. Communication should be precise, institution-grade, and accessible — never technical jargon without explanation.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                @foreach(['Professional', 'Authoritative', 'Trustworthy', 'Transparent'] as $trait)
                <div class="text-center p-4 rounded-xl bg-primary-50 border border-primary-100">
                    <span class="text-sm font-semibold text-primary-700">{{ $trait }}</span>
                </div>
                @endforeach
            </div>
        </section>

        <!-- ═══ 2. COLOR PALETTE ════════════════════════════════════════ -->
        <section id="colors">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                2. Color Palette
            </h2>

            <!-- Primary Blue -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-3">Primary Blue — Trust & Authority</h3>
                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                    @foreach([
                        ['50','#EBF4FB','text-neutral-800'],
                        ['100','#D1E7F5','text-neutral-800'],
                        ['200','#A8D0EC','text-neutral-800'],
                        ['300','#7FB9E3','text-neutral-800'],
                        ['400','#528CBE','text-white'],
                        ['500','#3D78AB','text-white'],
                        ['600','#2D6499','text-white'],
                        ['700','#1F5080','text-white'],
                        ['800','#143C66','text-white'],
                        ['900','#0A284D','text-white'],
                    ] as [$shade, $hex, $text])
                    <div class="rounded-lg overflow-hidden">
                        <div class="h-16 flex items-end justify-center pb-1" style="background:{{ $hex }}">
                            <span class="text-xs font-mono {{ $text }}">{{ $shade }}</span>
                        </div>
                        <div class="bg-white px-1 py-1 text-center">
                            <span class="text-xs font-mono text-neutral-500">{{ $hex }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Gold -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-3">Gold — Excellence & Prestige</h3>
                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                    @foreach([
                        ['50','#FEF9EC','text-neutral-800'],
                        ['100','#FDF0CA','text-neutral-800'],
                        ['200','#FBE196','text-neutral-800'],
                        ['300','#F8CC5E','text-neutral-800'],
                        ['400','#F0B43C','text-neutral-800'],
                        ['500','#E09A1F','text-white'],
                        ['600','#C07E15','text-white'],
                        ['700','#9A620F','text-white'],
                        ['800','#764A09','text-white'],
                        ['900','#533406','text-white'],
                    ] as [$shade, $hex, $text])
                    <div class="rounded-lg overflow-hidden">
                        <div class="h-16 flex items-end justify-center pb-1" style="background:{{ $hex }}">
                            <span class="text-xs font-mono {{ $text }}">{{ $shade }}</span>
                        </div>
                        <div class="bg-white px-1 py-1 text-center">
                            <span class="text-xs font-mono text-neutral-500">{{ $hex }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Neutral -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-3">Neutral Gray — Structure & Content</h3>
                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                    @foreach([
                        ['50','#F9FAFB','text-neutral-800'],
                        ['100','#F3F4F6','text-neutral-800'],
                        ['200','#D9D9D9','text-neutral-800'],
                        ['300','#C4C4C4','text-neutral-800'],
                        ['400','#9CA3AF','text-neutral-800'],
                        ['500','#6B7280','text-white'],
                        ['600','#4B5563','text-white'],
                        ['700','#374151','text-white'],
                        ['800','#1F2937','text-white'],
                        ['900','#111827','text-white'],
                    ] as [$shade, $hex, $text])
                    <div class="rounded-lg overflow-hidden">
                        <div class="h-16 flex items-end justify-center pb-1" style="background:{{ $hex }}">
                            <span class="text-xs font-mono {{ $text }}">{{ $shade }}</span>
                        </div>
                        <div class="bg-white px-1 py-1 text-center">
                            <span class="text-xs font-mono text-neutral-500">{{ $hex }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Semantic Colors -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['Success','#16A34A','bg-success-600'],
                    ['Warning','#D97706','bg-warning-600'],
                    ['Danger','#DC2626','bg-danger-600'],
                    ['Info','#2563EB','bg-info-600'],
                ] as [$name, $hex, $bg])
                <div class="rounded-xl overflow-hidden border border-neutral-200">
                    <div class="h-20 {{ $bg }} flex items-center justify-center">
                        <span class="text-white font-semibold">{{ $name }}</span>
                    </div>
                    <div class="p-3 bg-white text-center">
                        <span class="text-xs font-mono text-neutral-500">{{ $hex }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Accessibility Note -->
            <div class="mt-6 alert alert-info">
                <i class="fas fa-info-circle mt-0.5"></i>
                <div>
                    <strong>Accessibility:</strong> Primary-400 (#528CBE) on white achieves a 4.5:1 contrast ratio (WCAG AA).
                    Primary-700 (#1F5080) on white achieves 7:1+ (WCAG AAA). Always use text-white on Primary-500 and darker.
                </div>
            </div>
        </section>

        <!-- ═══ 3. TYPOGRAPHY ═══════════════════════════════════════════ -->
        <section id="typography">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                3. Typography
            </h2>

            <div class="mt-8 card">
                <div class="card-header">
                    <span class="card-title">Font Family — Instrument Sans</span>
                    <span class="badge badge-primary">Primary</span>
                </div>
                <div class="card-body space-y-6">
                    <div>
                        <p class="text-xs text-neutral-400 font-mono mb-2">H1 — 3rem / 700</p>
                        <h1 class="text-5xl font-bold">Case Management Portal</h1>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400 font-mono mb-2">H2 — 2.25rem / 700</p>
                        <h2 class="text-4xl font-bold">Court Hearing Schedule</h2>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400 font-mono mb-2">H3 — 1.875rem / 600</p>
                        <h3 class="text-3xl font-semibold">Active Cases Overview</h3>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400 font-mono mb-2">H4 — 1.5rem / 600</p>
                        <h4 class="text-2xl font-semibold">Case Reference: CR/2025/0042</h4>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400 font-mono mb-2">H5 — 1.25rem / 600</p>
                        <h5 class="text-xl font-semibold">Assigned Judge: Hon. Ahmed Hassan</h5>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400 font-mono mb-2">Body — 1rem / 400</p>
                        <p class="text-base">The accused appeared before the court on the date specified and the presiding judge ordered a continuation pending further evidence submission by the prosecution team.</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400 font-mono mb-2">Small / Caption — 0.875rem / 400</p>
                        <p class="text-sm text-neutral-500">Last updated: 22 April 2025 · Court Registry No. 7</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ 4. BUTTONS ══════════════════════════════════════════════ -->
        <section id="buttons">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                4. Buttons
            </h2>

            <div class="mt-8 card">
                <div class="card-header"><span class="card-title">Button Variants</span></div>
                <div class="card-body space-y-6">
                    <!-- Variants -->
                    <div>
                        <p class="text-xs font-mono text-neutral-400 mb-3">Variants</p>
                        <div class="flex flex-wrap gap-3">
                            <button class="btn-primary"><i class="fas fa-save mr-1"></i> Primary</button>
                            <button class="btn-secondary"><i class="fas fa-star mr-1"></i> Secondary</button>
                            <button class="btn-outline"><i class="fas fa-eye mr-1"></i> Outline</button>
                            <button class="btn-ghost"><i class="fas fa-times mr-1"></i> Ghost</button>
                            <button class="btn-danger"><i class="fas fa-trash mr-1"></i> Danger</button>
                            <button class="btn-success"><i class="fas fa-check mr-1"></i> Success</button>
                        </div>
                    </div>
                    <!-- Sizes -->
                    <div>
                        <p class="text-xs font-mono text-neutral-400 mb-3">Sizes</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <button class="btn btn-sm bg-primary-400 text-white">Small</button>
                            <button class="btn-primary">Default</button>
                            <button class="btn btn-lg bg-primary-400 text-white">Large</button>
                        </div>
                    </div>
                    <!-- States -->
                    <div>
                        <p class="text-xs font-mono text-neutral-400 mb-3">States</p>
                        <div class="flex flex-wrap gap-3">
                            <button class="btn-primary" disabled>Disabled</button>
                            <button class="btn-primary flex items-center gap-2">
                                <span class="spinner w-4 h-4"></span> Loading…
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ 5. FORM COMPONENTS ══════════════════════════════════════ -->
        <section id="forms">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                5. Form Components
            </h2>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card card-body space-y-4">
                    <h3 class="card-title">Standard Form</h3>
                    <div class="form-group">
                        <label class="form-label">Case Number <span class="text-danger-500">*</span></label>
                        <input type="text" class="form-input" placeholder="e.g. CR/2025/0042">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Court</label>
                        <select class="form-select">
                            <option>Select court…</option>
                            <option>Supreme Court</option>
                            <option>High Court – Mogadishu</option>
                            <option>Regional Court</option>
                        </select>
                        <p class="form-hint">Select the originating court</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-textarea" placeholder="Additional case notes…"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invalid Field</label>
                        <input type="text" class="form-input-error" value="bad-input">
                        <p class="form-error"><i class="fas fa-exclamation-circle"></i> This field contains an error.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="confirm" class="form-checkbox">
                        <label for="confirm" class="text-sm text-neutral-700">I confirm the information is accurate</label>
                    </div>
                    <button class="btn-primary w-full">Submit Case</button>
                </div>

                <!-- Search/Filter Form -->
                <div class="card card-body space-y-4">
                    <h3 class="card-title">Search & Filter</h3>
                    <div class="form-group">
                        <label class="form-label">Search Cases</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3 text-neutral-400 text-sm"></i>
                            <input type="text" class="form-input pl-9" placeholder="Search by name, case number…">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select">
                                <option>All</option>
                                <option>Active</option>
                                <option>Closed</option>
                                <option>Pending</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-input">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn-primary flex-1"><i class="fas fa-search mr-2"></i>Search</button>
                        <button class="btn-ghost px-4"><i class="fas fa-undo"></i></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ 6. TABLES ═══════════════════════════════════════════════ -->
        <section id="tables">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                6. Tables — Case Management
            </h2>

            <div class="mt-8 card">
                <div class="card-header">
                    <span class="card-title">Active Cases</span>
                    <button class="btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> New Case</button>
                </div>
                <div class="table-wrapper rounded-none">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Parties</th>
                                <th>Court</th>
                                <th>Judge</th>
                                <th>Next Hearing</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['CR/2025/0042','State v. Hassan Ali','Supreme Court','Hon. Ibrahim Mohamed','28 Apr 2025','Active','danger'],
                                ['CR/2025/0038','Fadumo v. Telecorp Ltd','High Court','Hon. Amina Warsame','02 May 2025','Pending','warning'],
                                ['CR/2024/0195','Mukhtar Estate Case','Regional Court','Hon. Yusuf Aden','Completed','Closed','neutral'],
                                ['CR/2025/0041','State v. Omar Hashi','High Court','Hon. Ibrahim Mohamed','30 Apr 2025','Active','danger'],
                            ] as [$no,$parties,$court,$judge,$date,$status,$color])
                            <tr>
                                <td class="font-mono text-primary-600 font-medium">{{ $no }}</td>
                                <td class="font-medium">{{ $parties }}</td>
                                <td class="text-neutral-600">{{ $court }}</td>
                                <td class="text-neutral-600">{{ $judge }}</td>
                                <td class="text-neutral-600">{{ $date }}</td>
                                <td><span class="badge badge-{{ $color }}">{{ $status }}</span></td>
                                <td>
                                    <div class="flex gap-1">
                                        <button class="btn-ghost p-1.5 text-xs"><i class="fas fa-eye"></i></button>
                                        <button class="btn-ghost p-1.5 text-xs"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer flex items-center justify-between text-sm text-neutral-500">
                    <span>Showing 4 of 128 cases</span>
                    <div class="flex gap-1">
                        <button class="btn-ghost btn-sm">← Prev</button>
                        <button class="btn-primary btn-sm">1</button>
                        <button class="btn-ghost btn-sm">2</button>
                        <button class="btn-ghost btn-sm">3</button>
                        <button class="btn-ghost btn-sm">Next →</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ 7. ALERTS & BADGES ══════════════════════════════════════ -->
        <section id="alerts">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                7. Alerts, Badges & Notifications
            </h2>

            <div class="mt-8 space-y-4">
                <div class="alert alert-info"><i class="fas fa-info-circle mt-0.5"></i><div><strong>Hearing Scheduled:</strong> Case CR/2025/0042 has a hearing on 28 April 2025 at 09:00.</div></div>
                <div class="alert alert-success"><i class="fas fa-check-circle mt-0.5"></i><div><strong>Filed Successfully:</strong> Appeal document has been accepted and assigned reference AP/2025/011.</div></div>
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle mt-0.5"></i><div><strong>Deadline Approaching:</strong> Evidence submission for CR/2025/0038 is due in 3 days.</div></div>
                <div class="alert alert-danger"><i class="fas fa-times-circle mt-0.5"></i><div><strong>Access Denied:</strong> You do not have permission to view sealed court records.</div></div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <span class="badge badge-primary">Criminal</span>
                <span class="badge badge-gold">High Priority</span>
                <span class="badge badge-success">Closed</span>
                <span class="badge badge-warning">Pending</span>
                <span class="badge badge-danger">Active</span>
                <span class="badge badge-neutral">Draft</span>
            </div>
        </section>

        <!-- ═══ 8. DASHBOARD LAYOUT EXAMPLE ════════════════════════════ -->
        <section id="dashboard">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                8. Dashboard Header & Stats
            </h2>

            <div class="mt-8 rounded-2xl overflow-hidden border border-neutral-200 shadow-lg">
                <!-- App Bar -->
                <div class="bg-white border-b border-neutral-200 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary-400 flex items-center justify-center">
                            <i class="fas fa-balance-scale text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-primary-900">IECMS</span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-neutral-600">
                        <span><i class="fas fa-bell mr-1"></i> 3</span>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary-400 flex items-center justify-center text-white text-xs font-bold">JA</div>
                            <span>Judge Ahmed</span>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="bg-neutral-50 p-6">
                    <h3 class="page-title">Dashboard Overview</h3>
                    <p class="page-subtitle">Supreme Court of Somalia — April 2025</p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach([
                            ['fa-folder-open','Total Cases','1,284','bg-primary-500'],
                            ['fa-clock','Pending','342','bg-warning-600'],
                            ['fa-gavel','Active Hearings','56','bg-danger-600'],
                            ['fa-check-circle','Closed This Month','89','bg-success-600'],
                        ] as [$icon,$label,$val,$bg])
                        <div class="stat-card">
                            <div class="stat-icon {{ $bg }}">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <div>
                                <div class="stat-value">{{ $val }}</div>
                                <div class="stat-label">{{ $label }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ 9. CSS VARIABLES REFERENCE ════════════════════════════ -->
        <section id="css-vars">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                9. CSS Variables Reference
            </h2>
            <div class="mt-8 card card-body">
                <pre class="text-xs font-mono text-neutral-700 overflow-x-auto leading-relaxed bg-neutral-50 p-4 rounded-lg">:root {
  /* Brand Colors */
  --color-primary:     #528CBE;
  --color-gold:        #F0B43C;

  /* Typography */
  --font-sans:         'Instrument Sans', 'Inter', system-ui, sans-serif;

  /* Spacing (8px grid) */
  --space-2:  0.5rem;   /* 8px  — base unit */
  --space-4:  1rem;     /* 16px */
  --space-6:  1.5rem;   /* 24px */
  --space-8:  2rem;     /* 32px */

  /* Border Radius */
  --radius-md:  0.5rem;
  --radius-xl:  1rem;
  --radius-3xl: 1.5rem;

  /* Shadows */
  --shadow-card: 0 4px 20px 0 rgb(82 140 190 / 0.12);

  /* Layout */
  --sidebar-width: 260px;
  --header-height: 64px;
}</pre>
            </div>
        </section>

        <!-- ═══ 10. TAILWIND CLASS REFERENCE ══════════════════════════ -->
        <section id="tailwind" class="pb-20">
            <h2 class="text-3xl font-bold text-neutral-900 mb-2 pb-3 border-b-2 border-gold-400">
                10. Tailwind Class Reference
            </h2>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card card-body">
                    <h3 class="card-title mb-4">Component Classes</h3>
                    <pre class="text-xs font-mono text-neutral-700 bg-neutral-50 p-4 rounded-lg overflow-x-auto leading-loose">/* Buttons */
.btn-primary    .btn-secondary
.btn-outline    .btn-ghost
.btn-danger     .btn-success
.btn-sm         .btn-lg

/* Forms */
.form-input     .form-input-error
.form-label     .form-error
.form-select    .form-textarea
.form-hint      .form-checkbox

/* Cards */
.card           .card-header
.card-title     .card-body
.card-footer

/* Tables */
.table-wrapper  .table

/* Badges */
.badge-primary  .badge-gold
.badge-success  .badge-warning
.badge-danger   .badge-neutral

/* Alerts */
.alert-info     .alert-success
.alert-warning  .alert-danger

/* Layout */
.sidebar        .nav-item
.app-header     .page-content
.stat-card      .stat-icon</pre>
                </div>
                <div class="card card-body">
                    <h3 class="card-title mb-4">Color Utility Classes</h3>
                    <pre class="text-xs font-mono text-neutral-700 bg-neutral-50 p-4 rounded-lg overflow-x-auto leading-loose">/* Primary Blue */
bg-primary-{50..900}
text-primary-{50..900}
border-primary-{50..900}
ring-primary-{400,500}

/* Gold */
bg-gold-{50..900}
text-gold-{50..900}
border-gold-{50..900}

/* Neutral */
bg-neutral-{50..900}
text-neutral-{50..900}
border-neutral-{50..900}

/* Semantic */
bg-success-{50,100,500,600,700}
bg-warning-{50,100,500,600,700}
bg-danger-{50,100,500,600,700}
bg-info-{50,100,500,600,700}

/* Gradients */
.bg-brand-gradient
.bg-gold-gradient</pre>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-primary-900 text-primary-300 text-center py-8 text-sm">
        <p>IECMS Design System — Supreme Court of Somalia &nbsp;·&nbsp; Version 1.0</p>
        <p class="text-primary-400 text-xs mt-1">Primary: #528CBE &nbsp;·&nbsp; Gold: #F0B43C &nbsp;·&nbsp; Font: Instrument Sans</p>
    </footer>

</body>
</html>
