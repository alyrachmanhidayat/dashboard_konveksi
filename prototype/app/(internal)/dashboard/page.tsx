import Badge from "@/components/Badge";
import ProgressBar from "@/components/ProgressBar";
import StageDots from "@/components/StageDots";
import StatCard from "@/components/StatCard";
import { ClipboardIcon } from "@/components/icons";
import { formatDate, formatNumber } from "@/lib/format";
import { DEMO_TODAY, inProgressSpks, progressPercentage, urgencyTone } from "@/lib/dummy-data";

function daysLeft(deliveryDate: string) {
  const target = new Date(deliveryDate).getTime();
  const today = new Date(DEMO_TODAY).getTime();
  return Math.ceil((target - today) / (1000 * 60 * 60 * 24));
}

const toneLabel: Record<string, string> = {
  critical: "Kritis",
  warning: "Waspada",
  good: "Aman",
  info: "Longgar",
};

export default function DashboardPage() {
  const rows = [...inProgressSpks]
    .map((spk) => ({ spk, left: daysLeft(spk.deliveryDate) }))
    .sort((a, b) => a.left - b.left);

  const critical = rows.filter((r) => r.left <= 8).length;
  const warning = rows.filter((r) => r.left > 8 && r.left <= 10).length;
  const good = rows.filter((r) => r.left > 10 && r.left <= 12).length;

  return (
    <div className="space-y-6">
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard
          label="Total Order Aktif"
          value={String(rows.length)}
          hint="Sedang dalam proses produksi"
          accent="blue"
          icon={<ClipboardIcon className="h-5 w-5" />}
        />
        <StatCard
          label="Jatuh Tempo ≤ 8 Hari"
          value={String(critical)}
          hint="Perlu prioritas hari ini"
          accent="critical"
        />
        <StatCard
          label="Jatuh Tempo 9–10 Hari"
          value={String(warning)}
          hint="Pantau progres produksi"
          accent="warning"
        />
        <StatCard
          label="Jatuh Tempo 11–12 Hari"
          value={String(good)}
          hint="Masih sesuai jadwal"
          accent="good"
        />
      </div>

      <div className="rounded-xl border border-[#e1e0d9] bg-white shadow-sm">
        <div className="flex items-center justify-between border-b border-[#e1e0d9] px-5 py-4">
          <div>
            <h2 className="text-sm font-semibold text-[#0b0b0b]">SPK Dalam Proses</h2>
            <p className="text-xs text-[#898781]">Diurutkan berdasarkan tenggat waktu terdekat</p>
          </div>
          <span className="rounded-full bg-[#f4f3f0] px-3 py-1 text-xs font-medium text-[#52514e]">
            {rows.length} order
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full min-w-[880px] text-left text-sm">
            <thead>
              <tr className="border-b border-[#e1e0d9] text-xs uppercase tracking-wide text-[#898781]">
                <th className="px-5 py-3 font-medium">No. SPK / Order</th>
                <th className="px-5 py-3 font-medium">Qty</th>
                <th className="px-5 py-3 font-medium">Tenggat</th>
                <th className="px-5 py-3 font-medium">Progres Tahapan</th>
                <th className="px-5 py-3 font-medium">Selesai</th>
              </tr>
            </thead>
            <tbody>
              {rows.map(({ spk, left }) => (
                <tr key={spk.id} className="border-b border-[#eeede9] last:border-0 hover:bg-[#fafaf9]">
                  <td className="px-5 py-3.5">
                    <p className="font-medium text-[#0b0b0b]">{spk.spkNumber}</p>
                    <p className="text-xs text-[#898781]">
                      {spk.customerName} &middot; {spk.orderName}
                    </p>
                  </td>
                  <td className="px-5 py-3.5 tabular-nums text-[#52514e]">{formatNumber(spk.totalQty)}</td>
                  <td className="px-5 py-3.5">
                    <p className="text-[#0b0b0b]">{formatDate(spk.deliveryDate)}</p>
                    <Badge tone={urgencyTone(left)}>
                      H-{left} &middot; {toneLabel[urgencyTone(left)]}
                    </Badge>
                  </td>
                  <td className="px-5 py-3.5">
                    <StageDots spk={spk} />
                  </td>
                  <td className="px-5 py-3.5">
                    <ProgressBar percent={progressPercentage(spk)} />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
