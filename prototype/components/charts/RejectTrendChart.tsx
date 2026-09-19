"use client";

import { Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";

type Point = { month: string; rejected: number };

function CustomTooltip({ active, payload, label }: { active?: boolean; payload?: { value: number }[]; label?: string }) {
  if (!active || !payload?.length) return null;
  return (
    <div className="rounded-lg border border-[#e1e0d9] bg-white px-3 py-2 text-xs shadow-md">
      <p className="font-medium text-[#0b0b0b]">{label}</p>
      <p className="mt-0.5 text-[#a8481f]">{payload[0].value} order ditolak</p>
    </div>
  );
}

export default function RejectTrendChart({ data }: { data: Point[] }) {
  return (
    <ResponsiveContainer width="100%" height={280}>
      <BarChart data={data} margin={{ top: 8, right: 12, bottom: 0, left: 0 }} barCategoryGap="30%">
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
          allowDecimals={false}
          width={28}
        />
        <Tooltip content={<CustomTooltip />} cursor={{ fill: "#eb6834", fillOpacity: 0.08 }} />
        <Bar dataKey="rejected" fill="#eb6834" radius={[4, 4, 0, 0]} maxBarSize={36} />
      </BarChart>
    </ResponsiveContainer>
  );
}
