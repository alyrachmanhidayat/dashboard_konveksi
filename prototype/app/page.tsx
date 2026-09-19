import Link from "next/link";
import { ChartIcon, ClipboardIcon, GridIcon, ReceiptIcon, WalletIcon } from "@/components/icons";

const flow = [
  { title: "Buat SPK", desc: "Input customer, ukuran, material, dan tanggal kirim dalam satu form." },
  { title: "Lacak Progres", desc: "Pantau tahapan desain, print, press, hingga pengiriman secara real-time." },
  { title: "Tutup / Reject", desc: "Finalisasi order yang selesai atau ditolak dengan validasi otomatis." },
  { title: "Atur Harga", desc: "Tetapkan harga per meter dan/atau per piece sesuai model bisnis." },
  { title: "Terbitkan Invoice", desc: "1 atau 2 invoice otomatis dibuat sesuai kombinasi harga." },
  { title: "Kelola Piutang", desc: "Catat pembayaran bertahap hingga lunas, lengkap dengan riwayat." },
];

const features = [
  {
    icon: GridIcon,
    title: "Dashboard Real-time",
    desc: "Ringkasan order aktif dan indikator tenggat waktu (kritis, waspada, aman) agar tim produksi tahu prioritas hari ini.",
    href: "/dashboard",
    accent: "text-[#1f5aa8] bg-[#e9f1fc]",
  },
  {
    icon: ClipboardIcon,
    title: "Manajemen SPK",
    desc: "Buat dan lacak Surat Perintah Kerja lengkap dengan rincian ukuran, material, dan tahapan produksi.",
    href: "/spk",
    accent: "text-[#4a3aa7] bg-[#eeecfa]",
  },
  {
    icon: ReceiptIcon,
    title: "Invoice Dual-Harga",
    desc: "Dukung penagihan berbasis meter kain dan/atau per piece dalam satu order — otomatis membuat invoice terpisah.",
    href: "/invoice",
    accent: "text-[#a8481f] bg-[#fbe9e0]",
  },
  {
    icon: WalletIcon,
    title: "Piutang & Pembayaran",
    desc: "Pantau tagihan belum lunas, catat pembayaran bertahap, dan lihat riwayat pelunasan secara rapi.",
    href: "/piutang",
    accent: "text-[#0a8a0a] bg-[#e6f7e6]",
  },
  {
    icon: ChartIcon,
    title: "Laporan Omzet & Reject",
    desc: "Grafik tren 12 bulan untuk omzet dan order ditolak, khusus untuk pemilik dan admin bisnis.",
    href: "/reports",
    accent: "text-[#1f5aa8] bg-[#e9f1fc]",
  },
];

export default function LandingPage() {
  return (
    <div className="flex min-h-screen flex-col bg-[#f9f9f7]">
      <header className="sticky top-0 z-10 border-b border-[#e1e0d9] bg-white/90 backdrop-blur">
        <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 md:px-8">
          <div className="flex items-center gap-2.5">
            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2a78d6] text-sm font-bold text-white">
              IS
            </div>
            <span className="font-poppins text-lg font-bold text-[#0b0b0b]">ISW Sportware</span>
          </div>
          <Link
            href="/dashboard"
            className="rounded-lg bg-[#2a78d6] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#1f5aa8]"
          >
            Lihat Demo Dashboard
          </Link>
        </div>
      </header>

      <main className="flex-1">
        <section className="mx-auto max-w-6xl px-4 pt-16 pb-14 text-center md:px-8 md:pt-24">
          <span className="inline-flex items-center rounded-full bg-[#eeecfa] px-3 py-1 text-xs font-medium text-[#4a3aa7]">
            Prototipe Produk &mdash; Data Contoh untuk Demo
          </span>
          <h1 className="font-poppins mx-auto mt-5 max-w-3xl text-4xl font-bold tracking-tight text-[#0b0b0b] md:text-5xl">
            Satu Dashboard untuk Seluruh Alur Produksi Konveksi
          </h1>
          <p className="mx-auto mt-5 max-w-2xl text-base text-[#52514e] md:text-lg">
            Dari SPK, pelacakan progres produksi, penerbitan invoice dengan skema harga per meter
            maupun per piece, sampai piutang dan laporan omzet &mdash; semua dalam satu sistem yang
            rapi dan mudah dipantau tim.
          </p>
          <div className="mt-8 flex flex-wrap items-center justify-center gap-3">
            <Link
              href="/dashboard"
              className="rounded-lg bg-[#2a78d6] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#1f5aa8]"
            >
              Jelajahi Demo Dashboard
            </Link>
            <a
              href="#fitur"
              className="rounded-lg border border-[#e1e0d9] bg-white px-5 py-3 text-sm font-semibold text-[#0b0b0b] hover:bg-[#f4f3f0]"
            >
              Lihat Fitur
            </a>
          </div>
        </section>

        <section className="border-y border-[#e1e0d9] bg-white py-12">
          <div className="mx-auto max-w-6xl px-4 md:px-8">
            <h2 className="font-poppins text-center text-2xl font-bold text-[#0b0b0b]">
              Alur Bisnis, Dari Order Masuk Sampai Lunas
            </h2>
            <div className="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {flow.map((step, i) => (
                <div key={step.title} className="relative rounded-xl border border-[#e1e0d9] p-5">
                  <div className="flex h-8 w-8 items-center justify-center rounded-full bg-[#2a78d6] text-sm font-bold text-white">
                    {i + 1}
                  </div>
                  <h3 className="mt-3 text-sm font-semibold text-[#0b0b0b]">{step.title}</h3>
                  <p className="mt-1 text-sm text-[#52514e]">{step.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section id="fitur" className="mx-auto max-w-6xl px-4 py-14 md:px-8">
          <h2 className="font-poppins text-center text-2xl font-bold text-[#0b0b0b]">Fitur Utama</h2>
          <p className="mx-auto mt-2 max-w-xl text-center text-sm text-[#52514e]">
            Klik salah satu kartu untuk membuka tampilan demonya langsung &mdash; semua data di
            dalamnya adalah contoh.
          </p>
          <div className="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {features.map((feature) => {
              const Icon = feature.icon;
              return (
                <Link
                  key={feature.title}
                  href={feature.href}
                  className="group rounded-xl border border-[#e1e0d9] bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                >
                  <div className={`flex h-10 w-10 items-center justify-center rounded-lg ${feature.accent}`}>
                    <Icon className="h-5 w-5" />
                  </div>
                  <h3 className="mt-4 text-sm font-semibold text-[#0b0b0b]">{feature.title}</h3>
                  <p className="mt-1.5 text-sm text-[#52514e]">{feature.desc}</p>
                  <span className="mt-3 inline-flex items-center gap-1 text-xs font-medium text-[#1f5aa8] group-hover:underline">
                    Buka demo &rarr;
                  </span>
                </Link>
              );
            })}
          </div>
        </section>

        <section className="border-t border-[#e1e0d9] bg-white py-14">
          <div className="mx-auto max-w-4xl px-4 text-center md:px-8">
            <h2 className="font-poppins text-2xl font-bold text-[#0b0b0b]">Siap Dipakai Tim Produksi Anda</h2>
            <p className="mt-3 text-sm text-[#52514e]">
              Dibangun di atas Laravel &amp; MySQL, dengan role admin terpisah untuk laporan keuangan,
              serta validasi otomatis agar order tidak ditutup sebelum siap kirim.
            </p>
            <div className="mt-6">
              <Link
                href="/dashboard"
                className="rounded-lg bg-[#2a78d6] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#1f5aa8]"
              >
                Mulai Jelajahi Demo
              </Link>
            </div>
          </div>
        </section>
      </main>

      <footer className="border-t border-[#e1e0d9] px-4 py-6 text-center text-xs text-[#898781] md:px-8">
        ISW Sportware &middot; Dashboard Konveksi &mdash; prototipe tampilan, seluruh data bersifat contoh.
      </footer>
    </div>
  );
}
