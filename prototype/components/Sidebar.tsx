"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { ChartIcon, ClipboardIcon, GridIcon, HomeIcon, ReceiptIcon, WalletIcon } from "./icons";

const navItems = [
  { href: "/dashboard", label: "Dashboard", icon: GridIcon },
  { href: "/spk", label: "SPK Aktif", icon: ClipboardIcon },
  { href: "/invoice", label: "Invoice", icon: ReceiptIcon },
  { href: "/piutang", label: "Piutang", icon: WalletIcon },
  { href: "/reports", label: "Laporan", icon: ChartIcon },
];

export default function Sidebar() {
  const pathname = usePathname();

  return (
    <aside className="hidden w-64 shrink-0 flex-col border-r border-[#e1e0d9] bg-white md:flex">
      <div className="flex h-16 items-center gap-2.5 border-b border-[#e1e0d9] px-6">
        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2a78d6] text-sm font-bold text-white">
          IS
        </div>
        <span className="font-poppins text-lg font-bold text-[#0b0b0b]">ISW Sportware</span>
      </div>

      <nav className="flex-1 space-y-1 px-3 py-5">
        {navItems.map((item) => {
          const active = pathname === item.href;
          const Icon = item.icon;
          return (
            <Link
              key={item.href}
              href={item.href}
              className={`flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors ${
                active
                  ? "bg-[#e9f1fc] text-[#1f5aa8]"
                  : "text-[#52514e] hover:bg-[#f4f3f0] hover:text-[#0b0b0b]"
              }`}
            >
              <Icon className={`h-5 w-5 ${active ? "text-[#2a78d6]" : "text-[#898781]"}`} />
              {item.label}
            </Link>
          );
        })}
      </nav>

      <div className="border-t border-[#e1e0d9] p-4">
        <Link
          href="/"
          className="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-[#52514e] hover:bg-[#f4f3f0]"
        >
          <HomeIcon className="h-4 w-4" />
          Kembali ke Beranda
        </Link>
        <p className="mt-3 rounded-lg bg-[#fef3dc] px-3 py-2 text-[11px] leading-snug text-[#8a5a00]">
          Mode Demo &mdash; seluruh data di bawah ini contoh, bukan data transaksi nyata.
        </p>
      </div>
    </aside>
  );
}
