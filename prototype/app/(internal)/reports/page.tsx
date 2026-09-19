"use client";

import { useState } from "react";
import RejectTrendChart from "@/components/charts/RejectTrendChart";
import RevenueTrendChart from "@/components/charts/RevenueTrendChart";
import StatCard from "@/components/StatCard";
import { formatDate, formatNumber, formatRupiah } from "@/lib/format";
import { omzetTrend, paidInvoices, rejectTrend, rejectedSpks } from "@/lib/dummy-data";

const tabs = [
  { key: "omzet", label: "Rekap Omzet" },
  { key: "reject", label: "Rekap Reject" },
] as const;

export default function ReportsPage() {
  const [tab, setTab] = useState<(typeof tabs)[number]["key"]>("omzet");

  const rejectNominal = rejectedSpks.reduce(
    (sum, spk) => sum + spk.totalMeter * (spk.pricePerMeter ?? 0),
    0
  );
  const rejectQty = rejectedSpks.reduce((sum, spk) => sum + spk.totalQty, 0);
  const rejectMeter = rejectedSpks.reduce((sum, spk) => sum + spk.totalMeter, 0);

  return (
    <div className="space-y-6">
      <div className="inline-flex rounded-lg border border-[#e1e0d9] bg-white p-1">
        {tabs.map((t) => (
          <button
            key={t.key}
            onClick={() => setTab(t.key)}
            className={`rounded-md px-4 py-2 text-sm font-medium transition-colors ${
              tab === t.key ? "bg-[#2a78d6] text-white" : "text-[#52514e] hover:bg-[#f4f3f0]"
            }`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {tab === "omzet" ? (
        <div className="space-y-6">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Order Selesai (Sep 2026)" value="18" accent="blue" />
            <StatCard label="Total Omzet (Sep 2026)" value={formatRupiah(143_800_000)} accent="good" />
            <StatCard label="Total Qty (Sep 2026)" value={`${formatNumber(2140)} pcs`} accent="violet" />
            <StatCard label="Total Meter (Sep 2026)" value={`${formatNumber(1385)} m`} accent="orange" />
          </div>

          <div className="rounded-xl border border-[#e1e0d9] bg-white p-5 shadow-sm">
            <h2 className="text-sm font-semibold text-[#0b0b0b]">Tren Omzet 12 Bulan Terakhir</h2>
            <div className="mt-3">
              <RevenueTrendChart data={omzetTrend} />
            </div>
          </div>

          <div className="rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
            <div className="border-b border-[#e1e0d9] px-5 py-4">
              <h2 className="text-sm font-semibold text-[#0b0b0b]">Invoice Lunas Terbaru</h2>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full min-w-[700px] text-left text-sm">
                <thead>
                  <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                    <th className="px-5 py-3 font-medium">No. Invoice</th>
                    <th className="px-5 py-3 font-medium">Customer</th>
                    <th className="px-5 py-3 font-medium">Total</th>
                    <th className="px-5 py-3 font-medium">Tanggal</th>
                  </tr>
                </thead>
                <tbody>
                  {paidInvoices.map((inv) => (
                    <tr key={inv.id} className="border-b border-[#eeede9] last:border-0">
                      <td className="px-5 py-3.5 font-medium text-[#0b0b0b]">{inv.invoiceNumber}</td>
                      <td className="px-5 py-3.5 text-[#52514e]">{inv.customerName}</td>
                      <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatRupiah(inv.totalAmount)}</td>
                      <td className="px-5 py-3.5 text-[#52514e]">{formatDate(inv.issuedDate)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      ) : (
        <div className="space-y-6">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Order Ditolak (Sep 2026)" value={String(rejectedSpks.length)} accent="critical" />
            <StatCard label="Total Nominal" value={formatRupiah(rejectNominal)} accent="critical" />
            <StatCard label="Total Qty" value={`${formatNumber(rejectQty)} pcs`} accent="warning" />
            <StatCard label="Total Meter" value={`${formatNumber(rejectMeter)} m`} accent="warning" />
          </div>

          <div className="rounded-xl border border-[#e1e0d9] bg-white p-5 shadow-sm">
            <h2 className="text-sm font-semibold text-[#0b0b0b]">Tren Reject 12 Bulan Terakhir</h2>
            <div className="mt-3">
              <RejectTrendChart data={rejectTrend} />
            </div>
          </div>

          <div className="rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
            <div className="border-b border-[#e1e0d9] px-5 py-4">
              <h2 className="text-sm font-semibold text-[#0b0b0b]">SPK Ditolak</h2>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full min-w-[760px] text-left text-sm">
                <thead>
                  <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                    <th className="px-5 py-3 font-medium">No. SPK</th>
                    <th className="px-5 py-3 font-medium">Customer / Order</th>
                    <th className="px-5 py-3 font-medium">Total Meter</th>
                    <th className="px-5 py-3 font-medium">Tanggal Ditolak</th>
                  </tr>
                </thead>
                <tbody>
                  {rejectedSpks.map((spk) => (
                    <tr key={spk.id} className="border-b border-[#eeede9] last:border-0">
                      <td className="px-5 py-3.5 font-medium text-[#0b0b0b]">{spk.spkNumber}</td>
                      <td className="px-5 py-3.5">
                        <p className="text-[#0b0b0b]">{spk.customerName}</p>
                        <p className="text-xs text-[#898781]">{spk.orderName}</p>
                      </td>
                      <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatNumber(spk.totalMeter)} m</td>
                      <td className="px-5 py-3.5 text-[#52514e]">{formatDate(spk.closedDate ?? spk.deliveryDate)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
