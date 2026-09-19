const fillClasses: Record<string, string> = {
  full: "bg-[#0ca30c]",
  mid: "bg-[#2a78d6]",
  low: "bg-[#fab219]",
  none: "bg-[#c3c2b7]",
};

export default function ProgressBar({ percent }: { percent: number }) {
  const tone = percent >= 100 ? "full" : percent >= 50 ? "mid" : percent > 0 ? "low" : "none";
  return (
    <div className="flex items-center gap-2">
      <div className="h-2 w-28 overflow-hidden rounded-full bg-[#eeede9]">
        <div
          className={`h-full rounded-full ${fillClasses[tone]}`}
          style={{ width: `${percent}%` }}
        />
      </div>
      <span className="text-xs font-medium text-[#52514e] tabular-nums">{percent}%</span>
    </div>
  );
}
