// Template: Official Governance Log PBL (ilm-inspired Minimalist Academic Style)
// Strictly aligned with Polinema Jobsheet 5 & Proposal PBL
// Zero signatures, zero blank evaluation boxes, typography-first, no em dashes

#let pbl_logbook(
  week_number: 5,
  period: "21 Sep 2026 - 27 Sep 2026",
  sprint_name: "Sprint 1: Core Infrastructure",
  checkpoint_target: "Checkpoint 2 (Minggu ke-8)",
  project_title: "VALENIA (Verifikasi Antrian & Layanan Navigasi Interaktif Poliklinik)",
  institution: "POLITEKNIK NEGERI MALANG",
  department: "JURUSAN TEKNOLOGI INFORMASI",
  study_program: "PROGRAM STUDI D-IV TEKNIK INFORMATIKA",
  class_name: "TI-2H",
  academic_year: "2026/2027",
  supervisor_name: "Titis Wahyudi, S.Kom., M.Kom.",
  supervisor_nip: "-",
  repository: "https://github.com/hafidzrafi/valenia",
  decisions: (),
  activities: (),
  evaluations: (),
  body
) = {
  // Document Configuration
  set document(title: "Governance Log PBL Minggu " + str(week_number) + " - VALENIA", author: "Tim PBL VALENIA")
  set page(
    paper: "a4",
    margin: (x: 2cm, top: 1.8cm, bottom: 1.8cm),
    header: context {
      if counter(page).get().first() > 1 {
        grid(
          columns: (1fr, auto),
          align(left)[#text(size: 8pt, fill: rgb("#64748b"))[VALENIA • Governance Log PBL (Minggu ke-#week_number)]],
          align(right)[#text(size: 8pt, fill: rgb("#94a3b8"))[#sprint_name]]
        )
        v(-2pt)
        line(length: 100%, stroke: 0.4pt + rgb("#e2e8f0"))
      }
    },
    footer: context {
      let page_num = counter(page).get().first()
      let total_pages = counter(page).final().first()
      grid(
        columns: (1fr, auto),
        align(left)[#text(size: 8pt, fill: rgb("#94a3b8"))[Repositori: #link(repository)[#repository.replace("https://", "")]]],
        align(right)[#text(size: 8pt, fill: rgb("#64748b"))[Halaman #page_num / #total_pages]]
      )
    }
  )

  set text(font: "Arial", size: 9pt, fill: rgb("#0f172a"), lang: "id")
  set par(justify: true, leading: 0.75em)

  show link: it => {
    set text(rgb("#2563eb"))
    it
  }

  // -------------------------------------------------------------
  // HEADER (ilm Minimalist Header)
  // -------------------------------------------------------------
  grid(
    columns: (38pt, 1fr),
    gutter: 10pt,
    align: (center + horizon, left + horizon),
    image("assets/logo.png", width: 36pt),
    [
      #text(size: 7.5pt, weight: "medium", fill: rgb("#64748b"), tracking: 0.5pt)[
        #upper(institution) • #upper(department)
      ] \
      #v(1.5pt)
      #text(size: 13.5pt, weight: "bold", fill: rgb("#0f172a"))[GOVERNANCE LOG - VALENIA] \
      #v(0.5pt)
      #text(size: 8.5pt, fill: rgb("#334155"))[#project_title]
    ]
  )

  v(4pt)
  line(length: 100%, stroke: 0.6pt + rgb("#0f172a"))
  v(4pt)

  // -------------------------------------------------------------
  // METADATA ROW
  // -------------------------------------------------------------
  grid(
    columns: (1fr, 1fr, 1.2fr),
    column-gutter: 12pt,
    row-gutter: 3pt,
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[Sprint Aktif] \
      #text(size: 8.5pt, weight: "bold", fill: rgb("#0f172a"))[#sprint_name]
    ],
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[Rentang Periode] \
      #text(size: 8.5pt, weight: "medium", fill: rgb("#0f172a"))[#period]
    ],
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[Target Milestone] \
      #text(size: 8.5pt, weight: "medium", fill: rgb("#0f172a"))[#checkpoint_target]
    ],
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[Dosen Pembimbing] \
      #text(size: 8.5pt, weight: "medium", fill: rgb("#0f172a"))[#supervisor_name]
    ],
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[Kelas & Kelompok] \
      #text(size: 8.5pt, weight: "medium", fill: rgb("#0f172a"))[#class_name / Tim 1]
    ],
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[Tahun Akademik] \
      #text(size: 8.5pt, weight: "medium", fill: rgb("#0f172a"))[#academic_year]
    ],
  )

  v(4pt)
  line(length: 100%, stroke: 0.4pt + rgb("#e2e8f0"))
  v(4pt)

  // -------------------------------------------------------------
  // TEAM ROSTER (Proposal PBL Aligned)
  // -------------------------------------------------------------
  text(size: 9pt, weight: "bold", fill: rgb("#0f172a"))[Susunan Tim Pengembang (Kelompok 1)]
  v(2.5pt)

  grid(
    columns: (1.1fr, 0.9fr, 1.1fr, 1.1fr),
    column-gutter: 8pt,
    [
      #text(size: 8pt, weight: "bold")[Raditya Mahatma G.] \
      #text(size: 7pt, fill: rgb("#64748b"))[Ketua Tim & Pengembang Utama]
    ],
    [
      #text(size: 8pt, weight: "bold")[Mohammad Hafidz R. R.] \
      #text(size: 7pt, fill: rgb("#64748b"))[QA / Penguji]
    ],
    [
      #text(size: 8pt, weight: "bold")[Galuh Pramudya A.] \
      #text(size: 7pt, fill: rgb("#64748b"))[Analis / Desainer]
    ],
    [
      #text(size: 8pt, weight: "bold")[Findi Finanda A.] \
      #text(size: 7pt, fill: rgb("#64748b"))[Sekretaris / Dokumentator]
    ],
  )

  v(4pt)
  line(length: 100%, stroke: 0.4pt + rgb("#e2e8f0"))
  v(5pt)

  // -------------------------------------------------------------
  // TABEL 1: LOG KEPUTUSAN TATA KELOLA PROYEK
  // -------------------------------------------------------------
  grid(
    columns: (1fr, auto),
    align: (left + horizon, right + horizon),
    [
      #text(size: 9pt, weight: "bold", fill: rgb("#0f172a"))[1. Log Keputusan Tata Kelola Proyek]
    ],
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[#decisions.len() keputusan tercatat]
    ]
  )
  v(2.5pt)

  if decisions.len() > 0 {
    table(
      columns: (65pt, 110pt, 1fr),
      stroke: (x, y) => if y == 0 {
        (bottom: 0.8pt + rgb("#0f172a"), top: 0.8pt + rgb("#0f172a"))
      } else {
        (bottom: 0.4pt + rgb("#e2e8f0"))
      },
      fill: none,
      inset: (x: 4pt, y: 3.5pt),
      align: (col, row) => (
        if row == 0 { center + horizon }
        else if col == 0 { center + horizon }
        else { left + horizon }
      ),
      table.header(
        [#text(size: 8pt, weight: "bold")[Tanggal]],
        [#text(size: 8pt, weight: "bold")[Pengambil Keputusan]],
        [#text(size: 8pt, weight: "bold")[Keputusan & Alasan]],
      ),
      ..decisions.map(dec => (
        [#text(size: 7.5pt)[#dec.at("date", default: "-")]],
        [#text(size: 7.5pt, weight: "medium")[#dec.at("decision_maker", default: "-")]],
        [
          #text(size: 8pt, weight: "bold", fill: rgb("#0f172a"))[#dec.at("decision", default: "-")]
          #if "rationale" in dec and dec.rationale != "" and dec.rationale != "-" [
            \ #text(size: 7pt, fill: rgb("#475569"))[#dec.rationale]
          ]
        ],
      )).flatten()
    )
  } else {
    rect(
      stroke: 0.4pt + rgb("#e2e8f0"),
      fill: rgb("#fafafa"),
      width: 100%,
      inset: (x: 6pt, y: 4pt),
      [
        #text(style: "italic", size: 7.5pt, fill: rgb("#64748b"))[Belum ada keputusan tata kelola yang dicatat pada periode ini.]
      ]
    )
  }

  v(5pt)

  // -------------------------------------------------------------
  // TABEL 2: REKAPITULASI AKTIVITAS PENGERJAAN
  // -------------------------------------------------------------
  let total_hours = activities.map(a => a.at("hours", default: 0)).sum(default: 0)

  grid(
    columns: (1fr, auto),
    align: (left + horizon, right + horizon),
    [
      #text(size: 9pt, weight: "bold", fill: rgb("#0f172a"))[2. Rekapitulasi Aktivitas Pengerjaan]
    ],
    [
      #text(size: 7.5pt, fill: rgb("#64748b"))[#activities.len() aktivitas • #total_hours jam kerja]
    ]
  )
  v(2.5pt)

  if activities.len() > 0 {
    table(
      columns: (58pt, 52pt, 1fr, 72pt, 28pt),
      stroke: (x, y) => if y == 0 {
        (bottom: 0.8pt + rgb("#0f172a"), top: 0.8pt + rgb("#0f172a"))
      } else {
        (bottom: 0.4pt + rgb("#e2e8f0"))
      },
      fill: none,
      inset: (x: 4pt, y: 3.5pt),
      align: (col, row) => (
        if row == 0 { center + horizon }
        else if col == 0 or col == 4 { center + horizon }
        else { left + horizon }
      ),
      table.header(
        [#text(size: 8pt, weight: "bold")[Tanggal]],
        [#text(size: 8pt, weight: "bold")[Pelaksana]],
        [#text(size: 8pt, weight: "bold")[Uraian Aktivitas]],
        [#text(size: 8pt, weight: "bold")[Tautan Bukti]],
        [#text(size: 8pt, weight: "bold")[Jam]],
      ),
      ..activities.map(act => (
        [#text(size: 7.5pt)[#act.at("date", default: "-")]],
        [#text(size: 7.5pt, weight: "medium")[#act.at("member", default: act.at("assignee", default: "-"))]],
        [#text(size: 8pt, weight: "bold", fill: rgb("#0f172a"))[#act.at("task", default: "-")]],
        if "link" in act and act.link != "" {
          text(size: 7pt, font: "DejaVu Sans Mono")[#link(act.link)[#act.at("evidence_label", default: "Link")]]
        } else {
          text(size: 7pt, fill: rgb("#94a3b8"))[-]
        },
        [#text(size: 7.5pt)[#act.at("hours", default: 0) h]],
      )).flatten()
    )
  } else {
    rect(
      stroke: 0.4pt + rgb("#e2e8f0"),
      fill: rgb("#fafafa"),
      width: 100%,
      inset: (x: 6pt, y: 4pt),
      [
        #text(style: "italic", size: 7.5pt, fill: rgb("#64748b"))[Belum ada aktivitas tercatat pada minggu ini.]
      ]
    )
  }
}
