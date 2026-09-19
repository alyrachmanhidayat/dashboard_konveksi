"use client";

import { useState } from "react";
import Badge from "@/components/Badge";
import Modal from "@/components/Modal";
import Toast from "@/components/Toast";
import { formatNumber, formatRupiah } from "@/lib/format";
import { closedNeedingPriceSpks, closedReadyForInvoiceSpks, type Spk } from "@/lib/dummy-data";

let invoiceSeq = 5;

export default function InvoicePage() {
  const [needingPrice, setNeedingPrice] = useState<Spk[]>(closedNeedingPriceSpks);
  const [ready, setReady] = useState<Spk[]>(closedReadyForInvoiceSpks);
  const [selectedId, setSelectedId] = useState<number | null>(ready[0]?.id ?? null);
  const [priceModalSpk, setPriceModalSpk] = useState<Spk | null>(null);
  const [priceForm, setPriceForm] = useState({ pricePerMeter: "", hargaPerPiece: "" });
  const [toast, setToast] = useState<string | null>(null);

  function showToast(message: string) {
    setToast(message);
    setTimeout(() => setToast(null), 3800);
  }

  function openPriceModal(spk: Spk) {
    setPriceModalSpk(spk);
    setPriceForm({ pricePerMeter: "", hargaPerPiece: "" });
  }

  function savePrice(e: React.FormEvent) {
    e.preventDefault();
    if (!priceModalSpk) return;
    if (!priceForm.pricePerMeter && !priceForm.hargaPerPiece) {
      showToast("Isi minimal salah satu: harga per meter atau per piece.");
      return;
    }
    const updated: Spk = {
      ...priceModalSpk,
      pricePerMeter: priceForm.pricePerMeter ? Number(priceForm.pricePerMeter) : null,
      hargaPerPiece: priceForm.hargaPerPiece ? Number(priceForm.hargaPerPiece) : null,
    };
    setNeedingPrice((prev) => prev.filter((s) => s.id !== priceModalSpk.id));
    setReady((prev) => [updated, ...prev]);
    setPriceModalSpk(null);
    showToast(`Harga untuk ${updated.spkNumber} tersimpan, siap diterbitkan invoice.`);
  }

  function publishInvoice() {
    const spk = ready.find((s) => s.id === selectedId);
    if (!spk) {
      showToast("Pilih satu SPK terlebih dahulu.");
      return;
    }
    const created: string[] = [];
    if (spk.pricePerMeter) {
      invoiceSeq += 1;
      created.push(`INV/MTR/09/2026/${String(invoiceSeq).padStart(4, "0")}`);
    }
    if (spk.hargaPerPiece) {
      invoiceSeq += 1;
      created.push(`INV/QTY/09/2026/${String(invoiceSeq).padStart(4, "0")}`);
    }
    setReady((prev) => prev.filter((s) => s.id !== spk.id));
    setSelectedId(null);
    showToast(`${created.length > 1 ? "2 invoice diterbitkan" : "Invoice diterbitkan"}: ${created.join(" & ")}`);
  }

  return (
    <div className="space-y-8">
      {needingPrice.length > 0 && (
        <section className="rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
          <div className="border-b border-[#e1e0d9] px-5 py-4">
            <h2 className="text-sm font-semibold text-[#0b0b0b]">Menunggu Penentuan Harga</h2>
            <p className="text-xs text-[#898781]">SPK yang sudah ditutup, belum punya harga per meter / per piece.</p>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full min-w-[720px] text-left text-sm">
              <thead>
                <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                  <th className="px-5 py-3 font-medium">No. SPK / Order</th>
                  <th className="px-5 py-3 font-medium">Total Meter</th>
                  <th className="px-5 py-3 font-medium">Total Qty</th>
                  <th className="px-5 py-3 font-medium" />
                </tr>
              </thead>
              <tbody>
                {needingPrice.map((spk) => (
                  <tr key={spk.id} className="border-b border-[#eeede9] last:border-0 hover:bg-[#fafaf9]">
                    <td className="px-5 py-3.5">
                      <p className="font-medium text-[#0b0b0b]">{spk.spkNumber}</p>
                      <p className="text-xs text-[#898781]">
                        {spk.customerName} &middot; {spk.orderName}
                      </p>
                    </td>
                    <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatNumber(spk.totalMeter)} m</td>
                    <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatNumber(spk.totalQty)} pcs</td>
                    <td className="px-5 py-3.5 text-right">
                      <button
                        onClick={() => openPriceModal(spk)}
                        className="rounded-lg border border-[#2a78d6] px-3 py-1.5 text-xs font-medium text-[#1f5aa8] hover:bg-[#e9f1fc]"
                      >
                        Atur Harga
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>
      )}

      <section className="rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-[#e1e0d9] px-5 py-4">
          <div>
            <h2 className="text-sm font-semibold text-[#0b0b0b]">Siap Diterbitkan Invoice</h2>
            <p className="text-xs text-[#898781]">
              Pilih satu SPK &mdash; sistem otomatis membuat 1 atau 2 invoice sesuai harga yang tersedia.
            </p>
          </div>
          <button
            onClick={publishInvoice}
            disabled={selectedId === null}
            className="rounded-lg bg-[#2a78d6] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#1f5aa8] disabled:cursor-not-allowed disabled:bg-[#c3c2b7]"
          >
            Terbitkan &amp; Cetak
          </button>
        </div>

        {ready.length === 0 ? (
          <p className="px-5 py-10 text-center text-sm text-[#898781]">
            Belum ada SPK yang siap diterbitkan invoice.
          </p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[900px] text-left text-sm">
              <thead>
                <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                  <th className="px-5 py-3 font-medium" />
                  <th className="px-5 py-3 font-medium">No. SPK / Order</th>
                  <th className="px-5 py-3 font-medium">Harga @ Meter / Piece</th>
                  <th className="px-5 py-3 font-medium">Nilai Invoice</th>
                  <th className="px-5 py-3 font-medium">Tipe</th>
                </tr>
              </thead>
              <tbody>
                {ready.map((spk) => {
                  const mtrValue = spk.pricePerMeter ? spk.totalMeter * spk.pricePerMeter : 0;
                  const qtyValue = spk.hargaPerPiece ? spk.totalQty * spk.hargaPerPiece : 0;
                  return (
                    <tr key={spk.id} className="border-b border-[#eeede9] last:border-0 hover:bg-[#fafaf9]">
                      <td className="px-5 py-3.5">
                        <input
                          type="radio"
                          name="selected-spk"
                          checked={selectedId === spk.id}
                          onChange={() => setSelectedId(spk.id)}
                          className="h-4 w-4 accent-[#2a78d6]"
                        />
                      </td>
                      <td className="px-5 py-3.5">
                        <p className="font-medium text-[#0b0b0b]">{spk.spkNumber}</p>
                        <p className="text-xs text-[#898781]">
                          {spk.customerName} &middot; {spk.orderName}
                        </p>
                      </td>
                      <td className="px-5 py-3.5 text-[#52514e]">
                        {spk.pricePerMeter && <p>{formatRupiah(spk.pricePerMeter)} / m</p>}
                        {spk.hargaPerPiece && <p>{formatRupiah(spk.hargaPerPiece)} / pcs</p>}
                      </td>
                      <td className="px-5 py-3.5 text-[#0b0b0b]">
                        {mtrValue > 0 && <p>{formatRupiah(mtrValue)} (MTR)</p>}
                        {qtyValue > 0 && <p>{formatRupiah(qtyValue)} (QTY)</p>}
                      </td>
                      <td className="px-5 py-3.5">
                        <div className="flex gap-1.5">
                          {spk.pricePerMeter && <Badge tone="info">MTR</Badge>}
                          {spk.hargaPerPiece && <Badge tone="violet">QTY</Badge>}
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </section>

      <Modal open={!!priceModalSpk} onClose={() => setPriceModalSpk(null)} title={`Atur Harga — ${priceModalSpk?.spkNumber ?? ""}`}>
        <form onSubmit={savePrice} className="space-y-4">
          <label className="block text-sm">
            <span className="mb-1 block font-medium text-[#52514e]">Harga per Meter (Rp)</span>
            <input
              type="number"
              min={0}
              value={priceForm.pricePerMeter}
              onChange={(e) => setPriceForm({ ...priceForm, pricePerMeter: e.target.value })}
              className="w-full rounded-lg border border-[#e1e0d9] px-3 py-2 text-sm outline-none focus:border-[#2a78d6]"
              placeholder="cth. 48000"
            />
          </label>
          <label className="block text-sm">
            <span className="mb-1 block font-medium text-[#52514e]">Harga per Piece (Rp)</span>
            <input
              type="number"
              min={0}
              value={priceForm.hargaPerPiece}
              onChange={(e) => setPriceForm({ ...priceForm, hargaPerPiece: e.target.value })}
              className="w-full rounded-lg border border-[#e1e0d9] px-3 py-2 text-sm outline-none focus:border-[#2a78d6]"
              placeholder="cth. 62000"
            />
          </label>
          <p className="text-xs text-[#898781]">Isi minimal salah satu. Setelah disimpan, harga tidak dapat diubah lagi.</p>
          <div className="flex justify-end gap-2 pt-1">
            <button
              type="button"
              onClick={() => setPriceModalSpk(null)}
              className="rounded-lg px-4 py-2 text-sm font-medium text-[#52514e] hover:bg-[#f4f3f0]"
            >
              Batal
            </button>
            <button type="submit" className="rounded-lg bg-[#2a78d6] px-4 py-2 text-sm font-medium text-white hover:bg-[#1f5aa8]">
              Simpan Harga
            </button>
          </div>
        </form>
      </Modal>

      {toast && <Toast message={toast} />}
    </div>
  );
}
