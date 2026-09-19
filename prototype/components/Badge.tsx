type Tone = "critical" | "warning" | "good" | "info" | "neutral" | "violet";

const toneClasses: Record<Tone, string> = {
  critical: "bg-[#fdecec] text-[#a82f2f] ring-1 ring-inset ring-[#d03b3b]/25",
  warning: "bg-[#fef3dc] text-[#8a5a00] ring-1 ring-inset ring-[#fab219]/40",
  good: "bg-[#e6f7e6] text-[#0a8a0a] ring-1 ring-inset ring-[#0ca30c]/25",
  info: "bg-[#e9f1fc] text-[#1f5aa8] ring-1 ring-inset ring-[#2a78d6]/25",
  neutral: "bg-[#eeede9] text-[#52514e] ring-1 ring-inset ring-[#898781]/25",
  violet: "bg-[#eeecfa] text-[#4a3aa7] ring-1 ring-inset ring-[#4a3aa7]/25",
};

export default function Badge({
  children,
  tone = "neutral",
}: {
  children: React.ReactNode;
  tone?: Tone;
}) {
  return (
    <span
      className={`inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap ${toneClasses[tone]}`}
    >
      {children}
    </span>
  );
}
