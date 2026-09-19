"use client";

export default function Toast({ message, tone = "good" }: { message: string; tone?: "good" | "info" }) {
  const toneClasses = tone === "good" ? "bg-[#0ca30c]" : "bg-[#2a78d6]";
  return (
    <div className="fixed bottom-6 left-1/2 z-50 -translate-x-1/2">
      <div className={`flex items-center gap-2 rounded-full px-4 py-2.5 text-sm font-medium text-white shadow-lg ${toneClasses}`}>
        {message}
      </div>
    </div>
  );
}
