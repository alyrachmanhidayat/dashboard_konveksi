"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { BellIcon } from "./icons";

const navItems = [
  { href: "/dashboard", label: "Dashboard" },
  { href: "/spk", label: "SPK Aktif" },
  { href: "/invoice", label: "Invoice" },
  { href: "/piutang", label: "Piutang" },
  { href: "/reports", label: "Laporan" },
];

const titles: Record<string, string> = {
  "/dashboard": "Dashboard",
  "/spk": "Surat Perintah Kerja (SPK)",
  "/invoice": "Penerbitan Invoice",
  "/piutang": "Piutang & Pembayaran",
  "/reports": "Laporan",
};

export default function Topbar() {
  const pathname = usePathname();
  const title = titles[pathname] ?? "Dashboard";

  return (
    <header className="sticky top-0 z-10 border-b border-[#e1e0d9] bg-white/90 backdrop-blur">
      <div className="flex h-16 items-center justify-between gap-3 px-4 md:px-8">
        <div className="flex items-center gap-2.5 md:hidden">
          <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2a78d6] text-sm font-bold text-white">
            IS
          </div>
          <span className="font-poppins text-base font-bold text-[#0b0b0b]">ISW Sportware</span>
        </div>
        <h1 className="hidden text-lg font-semibold text-[#0b0b0b] md:block">{title}</h1>

        <div className="flex items-center gap-3">
          <button
            type="button"
            className="flex h-9 w-9 items-center justify-center rounded-full text-[#52514e] hover:bg-[#f4f3f0]"
            aria-label="Notifikasi"
          >
            <BellIcon className="h-5 w-5" />
          </button>
          <div className="flex items-center gap-2 rounded-full border border-[#e1e0d9] py-1 pl-1 pr-3">
            <div className="flex h-7 w-7 items-center justify-center rounded-full bg-[#eeecfa] text-xs font-semibold text-[#4a3aa7]">
              AD
            </div>
            <span className="hidden text-sm font-medium text-[#0b0b0b] sm:inline">Admin</span>
          </div>
        </div>
      </div>

      <div className="flex gap-1 overflow-x-auto border-t border-[#e1e0d9] px-4 py-2 md:hidden">
        {navItems.map((item) => {
          const active = pathname === item.href;
          return (
            <Link
              key={item.href}
              href={item.href}
              className={`shrink-0 rounded-full px-3 py-1.5 text-xs font-medium ${
                active ? "bg-[#e9f1fc] text-[#1f5aa8]" : "text-[#52514e] bg-[#f4f3f0]"
              }`}
            >
              {item.label}
            </Link>
          );
        })}
      </div>
    </header>
  );
}
