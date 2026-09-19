"use client";

import { useState } from "react";
import Badge from "@/components/Badge";
import Modal from "@/components/Modal";
import Toast from "@/components/Toast";
import { formatDate, formatRupiah } from "@/lib/format";
import { paidInvoices, unpaidInvoices, type Invoice } from "@/lib/dummy-data";

export default function PiutangPage() {
  const [unpaid, setUnpaid] = useState<Invoice[]>(unpaidInvoices);
  const [paid, setPaid] = useState<Invoice[]>(paidInvoices);
  const [payModalInvoice, setPayModalInvoice] = useState<Invoice | null>(null);
  const [amount, setAmount] = useState("");
  const [toast, setToast] = useState<string | null>(null);

  function showToast(message: string) {
    setToast(message);
    setTimeout(() => setToast(null), 3500);
  }

  const totalOutstanding = unpaid.reduce((sum, inv) => sum + (inv.totalAmount - inv.paidAmount), 0);

  function openPay(inv: Invoice) {
    setPayModalInvoice(inv);
    setAmount(String(inv.totalAmount - inv.paidAmount));
  }

  function submitPayment(e: React.FormEvent) {
    e.preventDefault();
    if (!payModalInvoice) return;
    const value = Number(amount);
    if (!value || value <= 0) {
      showToast("Masukkan jumlah pembayaran yang valid.");
      return;
    }
    const newPaidAmount = payModalInvoice.paidAmount + value;
    const updated: Invoice = {
      ...payModalInvoice,
      paidAmount: Math.min(newPaidAmount, payModalInvoice.totalAmount),
      isPaid: newPaidAmount >= payModalInvoice.totalAmount,
      payments: [...payModalInvoice.payments, { date: "2026-09-19", amount: value }],
    };
    if (updated.isPaid) {
      setUnpaid((prev) => prev.filter((i) => i.id !== updated.id));
      setPaid((prev) => [updated, ...prev]);
      showToast(`${updated.invoiceNumber} lunas. Dipindahkan ke riwayat pembayaran.`);
    } else {
      setUnpaid((prev) => prev.map((i) => (i.id === updated.id ? updated : i)));
      showToast(`Pembayaran ${formatRupiah(value)} tercatat untuk ${updated.invoiceNumber}.`);
    }
    setPayModalInvoice(null);
  }

  return (
    <div className="space-y-8">
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div className="rounded-xl border border-[#e1e0d9] bg-white p-5 shadow-sm">
          <p className="text-xs font-medium uppercase tracking-wide text-[#898781]">Total Piutang</p>
          <p className="mt-1.5 text-2xl font-semibold text-[#0b0b0b]">{formatRupiah(totalOutstanding)}</p>
        </div>
        <div className="rounded-xl border border-[#e1e0d9] bg-white p-5 shadow-sm">
          <p className="text-xs font-medium uppercase tracking-wide text-[#898781]">Invoice Belum Lunas</p>
          <p className="mt-1.5 text-2xl font-semibold text-[#0b0b0b]">{unpaid.length}</p>
        </div>
        <div className="rounded-xl border border-[#e1e0d9] bg-white p-5 shadow-sm">
          <p className="text-xs font-medium uppercase tracking-wide text-[#898781]">Sudah Lunas</p>
          <p className="mt-1.5 text-2xl font-semibold text-[#0b0b0b]">{paid.length}</p>
        </div>
      </div>

      <section className="rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
        <div className="border-b border-[#e1e0d9] px-5 py-4">
          <h2 className="text-sm font-semibold text-[#0b0b0b]">Invoice Belum Lunas</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full min-w-[860px] text-left text-sm">
            <thead>
              <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                <th className="px-5 py-3 font-medium">No. Invoice</th>
                <th className="px-5 py-3 font-medium">Customer</th>
                <th className="px-5 py-3 font-medium">Total</th>
                <th className="px-5 py-3 font-medium">Terbayar</th>
                <th className="px-5 py-3 font-medium">Sisa</th>
                <th className="px-5 py-3 font-medium" />
              </tr>
            </thead>
            <tbody>
              {unpaid.map((inv) => (
                <tr key={inv.id} className="border-b border-[#eeede9] last:border-0 hover:bg-[#fafaf9]">
                  <td className="px-5 py-3.5">
                    <p className="font-medium text-[#0b0b0b]">{inv.invoiceNumber}</p>
                    <Badge tone={inv.type === "MTR" ? "info" : "violet"}>{inv.type}</Badge>
                  </td>
                  <td className="px-5 py-3.5">
                    <p className="text-[#0b0b0b]">{inv.customerName}</p>
                    <p className="text-xs text-[#898781]">{inv.orderName}</p>
                  </td>
                  <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatRupiah(inv.totalAmount)}</td>
                  <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatRupiah(inv.paidAmount)}</td>
                  <td className="px-5 py-3.5 tabular-nums font-medium text-[#a82f2f]">
                    {formatRupiah(inv.totalAmount - inv.paidAmount)}
                  </td>
                  <td className="px-5 py-3.5 text-right">
                    <button
                      onClick={() => openPay(inv)}
                      className="rounded-lg bg-[#2a78d6] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#1f5aa8]"
                    >
                      Bayar
                    </button>
                  </td>
                </tr>
              ))}
              {unpaid.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-5 py-10 text-center text-sm text-[#898781]">
                    Semua invoice sudah lunas.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </section>

      <section className="rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
        <div className="border-b border-[#e1e0d9] px-5 py-4">
          <h2 className="text-sm font-semibold text-[#0b0b0b]">Riwayat Lunas</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full min-w-[720px] text-left text-sm">
            <thead>
              <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                <th className="px-5 py-3 font-medium">No. Invoice</th>
                <th className="px-5 py-3 font-medium">Customer</th>
                <th className="px-5 py-3 font-medium">Total</th>
                <th className="px-5 py-3 font-medium">Tanggal Lunas</th>
              </tr>
            </thead>
            <tbody>
              {paid.map((inv) => (
                <tr key={inv.id} className="border-b border-[#eeede9] last:border-0 hover:bg-[#fafaf9]">
                  <td className="px-5 py-3.5">
                    <p className="font-medium text-[#0b0b0b]">{inv.invoiceNumber}</p>
                    <Badge tone={inv.type === "MTR" ? "info" : "violet"}>{inv.type}</Badge>
                  </td>
                  <td className="px-5 py-3.5 text-[#0b0b0b]">{inv.customerName}</td>
                  <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatRupiah(inv.totalAmount)}</td>
                  <td className="px-5 py-3.5 text-[#52514e]">
                    {formatDate(inv.payments[inv.payments.length - 1]?.date ?? inv.issuedDate)}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      <Modal open={!!payModalInvoice} onClose={() => setPayModalInvoice(null)} title={`Bayar — ${payModalInvoice?.invoiceNumber ?? ""}`}>
        <form onSubmit={submitPayment} className="space-y-4">
          {payModalInvoice && (
            <p className="text-sm text-[#52514e]">
              Sisa tagihan: <span className="font-semibold text-[#0b0b0b]">{formatRupiah(payModalInvoice.totalAmount - payModalInvoice.paidAmount)}</span>
            </p>
          )}
          <label className="block text-sm">
            <span className="mb-1 block font-medium text-[#52514e]">Jumlah Pembayaran (Rp)</span>
            <input
              type="number"
              min={0}
              value={amount}
              onChange={(e) => setAmount(e.target.value)}
              className="w-full rounded-lg border border-[#e1e0d9] px-3 py-2 text-sm outline-none focus:border-[#2a78d6]"
            />
          </label>
          <div className="flex justify-end gap-2 pt-1">
            <button
              type="button"
              onClick={() => setPayModalInvoice(null)}
              className="rounded-lg px-4 py-2 text-sm font-medium text-[#52514e] hover:bg-[#f4f3f0]"
            >
              Batal
            </button>
            <button type="submit" className="rounded-lg bg-[#2a78d6] px-4 py-2 text-sm font-medium text-white hover:bg-[#1f5aa8]">
              Catat Pembayaran
            </button>
          </div>
        </form>
      </Modal>

      {toast && <Toast message={toast} />}
    </div>
  );
}
