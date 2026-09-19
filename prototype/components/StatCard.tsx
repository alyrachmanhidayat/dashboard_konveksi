type Accent = "blue" | "critical" | "warning" | "good" | "violet" | "orange";

const barClasses: Record<Accent, string> = {
  blue: "bg-[#2a78d6]",
  critical: "bg-[#d03b3b]",
  warning: "bg-[#fab219]",
  good: "bg-[#0ca30c]",
  violet: "bg-[#4a3aa7]",
  orange: "bg-[#eb6834]",
};

const iconClasses: Record<Accent, string> = {
  blue: "bg-[#e9f1fc] text-[#1f5aa8]",
  critical: "bg-[#fdecec] text-[#a82f2f]",
  warning: "bg-[#fef3dc] text-[#8a5a00]",
  good: "bg-[#e6f7e6] text-[#0a8a0a]",
  violet: "bg-[#eeecfa] text-[#4a3aa7]",
  orange: "bg-[#fbe9e0] text-[#a8481f]",
};

export default function StatCard({
  label,
  value,
  hint,
  accent = "blue",
  icon,
}: {
  label: string;
  value: string;
  hint?: string;
  accent?: Accent;
  icon?: React.ReactNode;
}) {
  return (
    <div className="relative overflow-hidden rounded-xl border border-[#e1e0d9] bg-white p-5 shadow-sm">
      <span className={`absolute inset-y-0 left-0 w-1 ${barClasses[accent]}`} />
      <div className="flex items-start justify-between gap-3">
        <div>
          <p className="text-xs font-medium uppercase tracking-wide text-[#898781]">
            {label}
          </p>
          <p className="mt-1.5 text-2xl font-semibold text-[#0b0b0b]">{value}</p>
          {hint && <p className="mt-1 text-xs text-[#52514e]">{hint}</p>}
        </div>
        {icon && (
          <div className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${iconClasses[accent]}`}>
            {icon}
          </div>
        )}
      </div>
    </div>
  );
}
