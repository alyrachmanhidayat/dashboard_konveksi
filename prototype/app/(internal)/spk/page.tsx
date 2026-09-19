"use client";

import { useMemo, useState } from "react";
import Badge from "@/components/Badge";
import Modal from "@/components/Modal";
import StageDots from "@/components/StageDots";
import Toast from "@/components/Toast";
import { formatDate, formatNumber } from "@/lib/format";
import { DEMO_TODAY, inProgressSpks, type Spk, urgencyTone } from "@/lib/dummy-data";

const sizeFields = ["S", "M", "L", "XL", "XXL"] as const;

function nextSpkNumber(existing: Spk[]) {
  const seq = existing.length + 41;
  return `SPK/09/2026/${String(seq).padStart(4, "0")}`;
}

function daysLeft(deliveryDate: string) {
  const target = new Date(deliveryDate).getTime();
  const today = new Date(DEMO_TODAY).getTime();
  return Math.ceil((target - today) / (1000 * 60 * 60 * 24));
}

export default function SpkPage() {
  const [spks, setSpks] = useState<Spk[]>(inProgressSpks);
  const [open, setOpen] = useState(false);
  const [toast, setToast] = useState<string | null>(null);
  const [form, setForm] = useState({
    customerName: "",
    orderName: "",
    material: "",
    deliveryDate: "",
    S: "",
    M: "",
    L: "",
    XL: "",
    XXL: "",
  });

  const totalQtyPreview = useMemo(
    () => sizeFields.reduce((sum, key) => sum + (Number(form[key]) || 0), 0),
    [form]
  );

  function showToast(message: string) {
    setToast(message);
    setTimeout(() => setToast(null), 3500);
  }

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!form.customerName || !form.orderName || !form.deliveryDate) {
      showToast("Lengkapi customer, nama order, dan tanggal kirim.");
      return;
    }
    const spkNumber = nextSpkNumber(spks);
    const sizes = sizeFields
      .map((size) => ({ size, qty: Number(form[size]) || 0 }))
      .filter((row) => row.qty > 0);
    const newSpk: Spk = {
      id: Date.now(),
      spkNumber,
      customerName: form.customerName,
      orderName: form.orderName,
      material: form.material || "-",
      entryDate: DEMO_TODAY,
      deliveryDate: form.deliveryDate,
      sizes,
      totalQty: totalQtyPreview,
      totalMeter: 0,
      pricePerMeter: null,
      hargaPerPiece: null,
      status: "In Progress",
      isDesignDone: false,
      isPrintDone: false,
      isPressDone: false,
      isDeliveryDone: false,
    };
    setSpks((prev) => [newSpk, ...prev]);
    setOpen(false);
    setForm({ customerName: "", orderName: "", material: "", deliveryDate: "", S: "", M: "", L: "", XL: "", XXL: "" });
    showToast(`SPK ${spkNumber} berhasil dibuat (contoh, tidak tersimpan ke server).`);
  }

  function toggleStage(spkId: number, stage: keyof Pick<Spk, "isDesignDone" | "isPrintDone" | "isPressDone" | "isDeliveryDone">) {
    setSpks((prev) =>
      prev.map((s) => (s.id === spkId ? { ...s, [stage]: !s[stage] } : s))
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-sm font-semibold text-[#0b0b0b]">Daftar SPK Berjalan</h2>
          <p className="text-xs text-[#898781]">Klik tahapan pada tabel untuk mensimulasikan update progres.</p>
        </div>
        <button
          onClick={() => setOpen(true)}
          className="rounded-lg bg-[#2a78d6] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#1f5aa8]"
        >
          + Buat SPK Baru
        </button>
      </div>

      <div className="overflow-hidden rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full min-w-[960px] text-left text-sm">
            <thead>
              <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                <th className="px-5 py-3 font-medium">No. SPK</th>
                <th className="px-5 py-3 font-medium">Customer / Order</th>
                <th className="px-5 py-3 font-medium">Material</th>
                <th className="px-5 py-3 font-medium">Ukuran</th>
                <th className="px-5 py-3 font-medium">Tenggat</th>
                <th className="px-5 py-3 font-medium">Tahapan (klik untuk ubah)</th>
              </tr>
            </thead>
            <tbody>
              {spks.map((spk) => {
                const left = daysLeft(spk.deliveryDate);
                return (
                  <tr key={spk.id} className="border-b border-[#eeede9] last:border-0 hover:bg-[#fafaf9]">
                    <td className="px-5 py-3.5 font-medium text-[#0b0b0b]">{spk.spkNumber}</td>
                    <td className="px-5 py-3.5">
                      <p className="text-[#0b0b0b]">{spk.customerName}</p>
                      <p className="text-xs text-[#898781]">{spk.orderName}</p>
                    </td>
                    <td className="px-5 py-3.5 text-[#52514e]">{spk.material}</td>
                    <td className="px-5 py-3.5">
                      <div className="flex flex-wrap gap-1">
                        {spk.sizes.map((s) => (
                          <span
                            key={s.size}
                            className="rounded bg-[#f4f3f0] px-1.5 py-0.5 text-[11px] font-medium text-[#52514e]"
                          >
                            {s.size}:{s.qty}
                          </span>
                        ))}
                      </div>
                      <p className="mt-1 text-xs font-medium text-[#0b0b0b]">
                        Total {formatNumber(spk.totalQty)} pcs
                      </p>
                    </td>
                    <td className="px-5 py-3.5">
                      <p className="text-[#0b0b0b]">{formatDate(spk.deliveryDate)}</p>
                      <Badge tone={urgencyTone(left)}>H-{left}</Badge>
                    </td>
                    <td className="px-5 py-3.5">
                      <button onClick={() => toggleStage(spk.id, "isDesignDone")} className="cursor-pointer">
                        <StageDots
                          spk={{
                            isDesignDone: spk.isDesignDone,
                            isPrintDone: spk.isPrintDone,
                            isPressDone: spk.isPressDone,
                            isDeliveryDone: spk.isDeliveryDone,
                          }}
                        />
                      </button>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>

      <Modal open={open} onClose={() => setOpen(false)} title="Buat SPK Baru">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <label className="text-sm">
              <span className="mb-1 block font-medium text-[#52514e]">Nama Customer</span>
              <input
                value={form.customerName}
                onChange={(e) => setForm({ ...form, customerName: e.target.value })}
                className="w-full rounded-lg border border-[#e1e0d9] px-3 py-2 text-sm outline-none focus:border-[#2a78d6]"
                placeholder="PT Contoh Busana"
              />
            </label>
            <label className="text-sm">
              <span className="mb-1 block font-medium text-[#52514e]">Nama Order</span>
              <input
                value={form.orderName}
                onChange={(e) => setForm({ ...form, orderName: e.target.value })}
                className="w-full rounded-lg border border-[#e1e0d9] px-3 py-2 text-sm outline-none focus:border-[#2a78d6]"
                placeholder="Kaos Seragam Tim"
              />
            </label>
            <label className="text-sm">
              <span className="mb-1 block font-medium text-[#52514e]">Material</span>
              <input
                value={form.material}
                onChange={(e) => setForm({ ...form, material: e.target.value })}
                className="w-full rounded-lg border border-[#e1e0d9] px-3 py-2 text-sm outline-none focus:border-[#2a78d6]"
                placeholder="Cotton Combed 24s"
              />
            </label>
            <label className="text-sm">
              <span className="mb-1 block font-medium text-[#52514e]">Tanggal Kirim</span>
              <input
                type="date"
                value={form.deliveryDate}
                onChange={(e) => setForm({ ...form, deliveryDate: e.target.value })}
                className="w-full rounded-lg border border-[#e1e0d9] px-3 py-2 text-sm outline-none focus:border-[#2a78d6]"
              />
            </label>
          </div>

          <div>
            <span className="mb-1 block text-sm font-medium text-[#52514e]">Jumlah per Ukuran</span>
            <div className="grid grid-cols-5 gap-2">
              {sizeFields.map((size) => (
                <label key={size} className="text-center text-xs">
                  <span className="mb-1 block font-medium text-[#898781]">{size}</span>
                  <input
                    type="number"
                    min={0}
                    value={form[size]}
                    onChange={(e) => setForm({ ...form, [size]: e.target.value })}
                    className="w-full rounded-lg border border-[#e1e0d9] px-2 py-1.5 text-center text-sm outline-none focus:border-[#2a78d6]"
                  />
                </label>
              ))}
            </div>
            <p className="mt-2 text-xs text-[#898781]">Total qty: {formatNumber(totalQtyPreview)} pcs</p>
          </div>

          <div className="flex justify-end gap-2 pt-2">
            <button
              type="button"
              onClick={() => setOpen(false)}
              className="rounded-lg px-4 py-2 text-sm font-medium text-[#52514e] hover:bg-[#f4f3f0]"
            >
              Batal
            </button>
            <button
              type="submit"
              className="rounded-lg bg-[#2a78d6] px-4 py-2 text-sm font-medium text-white hover:bg-[#1f5aa8]"
            >
              Simpan SPK
            </button>
          </div>
        </form>
      </Modal>

      {toast && <Toast message={toast} />}
    </div>
  );
}
