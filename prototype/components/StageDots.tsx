const stages = [
  { key: "isDesignDone", label: "Desain" },
  { key: "isPrintDone", label: "Print" },
  { key: "isPressDone", label: "Press" },
  { key: "isDeliveryDone", label: "Kirim" },
] as const;

export default function StageDots({
  spk,
}: {
  spk: Record<(typeof stages)[number]["key"], boolean>;
}) {
  return (
    <div className="flex items-center gap-2">
      {stages.map((stage, i) => {
        const done = spk[stage.key];
        return (
          <div key={stage.key} className="flex items-center gap-2">
            <div
              title={stage.label}
              className={`flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold ${
                done ? "bg-[#0ca30c] text-white" : "bg-[#eeede9] text-[#898781]"
              }`}
            >
              {done ? "✓" : i + 1}
            </div>
            {i < stages.length - 1 && (
              <span className={`h-px w-3 ${done ? "bg-[#0ca30c]" : "bg-[#e1e0d9]"}`} />
            )}
          </div>
        );
      })}
    </div>
  );
}
