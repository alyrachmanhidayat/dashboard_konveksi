"use client";

import { Area, AreaChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";
import { formatRupiah } from "@/lib/format";

type Point = { month: string; revenue: number };

function CustomTooltip({ active, payload, label }: { active?: boolean; payload?: { value: number }[]; label?: string }) {
  if (!active || !payload?.length) return null;
  return (
    <div className="rounded-lg border border-[#e1e0d9] bg-white px-3 py-2 text-xs shadow-md">
      <p className="font-medium text-[#0b0b0b]">{label}</p>
      <p className="mt-0.5 text-[#1f5aa8]">{formatRupiah(payload[0].value)}</p>
    </div>
  );
}

export default function RevenueTrendChart({ data }: { data: Point[] }) {
  return (
    <ResponsiveContainer width="100%" height={280}>
      <AreaChart data={data} margin={{ top: 8, right: 12, bottom: 0, left: 0 }}>
        <defs>
          <linearGradient id="revenueFill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stopColor="#2a78d6" stopOpacity={0.22} />
            <stop offset="100%" stopColor="#2a78d6" stopOpacity={0} />
          </linearGradient>
        </defs>
        <CartesianGrid stroke="#e1e0d9" vertical={false} />
        <XAxis
          dataKey="month"
          tick={{ fill: "#898781", fontSize: 11 }}
          axisLine={{ stroke: "#c3c2b7" }}
          tickLine={false}
        />
        <YAxis
          tick={{ fill: "#898781", fontSize: 11 }}
          axisLine={false}
          tickLine={false}
          tickFormatter={(v) => `${(v / 1_000_000).toFixed(0)}jt`}
          width={44}
        />
        <Tooltip content={<CustomTooltip />} cursor={{ stroke: "#c3c2b7", strokeWidth: 1 }} />
        <Area
          type="monotone"
          dataKey="revenue"
          stroke="#2a78d6"
          strokeWidth={2}
          fill="url(#revenueFill)"
          dot={false}
          activeDot={{ r: 4, fill: "#2a78d6", stroke: "#fcfcfb", strokeWidth: 2 }}
        />
      </AreaChart>
    </ResponsiveContainer>
  );
}
