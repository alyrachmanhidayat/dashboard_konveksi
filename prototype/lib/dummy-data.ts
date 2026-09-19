// All data on this page is fictional, generated for a sales demo only.

export const DEMO_TODAY = "2026-09-19";

export type SpkSize = { size: string; qty: number };

export type Spk = {
  id: number;
  spkNumber: string;
  customerName: string;
  orderName: string;
  material: string;
  entryDate: string;
  deliveryDate: string;
  closedDate?: string;
  sizes: SpkSize[];
  totalQty: number;
  totalMeter: number;
  pricePerMeter: number | null;
  hargaPerPiece: number | null;
  status: "In Progress" | "Closed" | "Rejected";
  isDesignDone: boolean;
  isPrintDone: boolean;
  isPressDone: boolean;
  isDeliveryDone: boolean;
};

export type Invoice = {
  id: number;
  invoiceNumber: string;
  type: "MTR" | "QTY";
  spkId: number;
  customerName: string;
  orderName: string;
  totalQty: number;
  totalAmount: number;
  paidAmount: number;
  isPaid: boolean;
  issuedDate: string;
  payments: { date: string; amount: number }[];
};

const sizes = (s: number, m: number, l: number, xl: number, xxl = 0): SpkSize[] =>
  [
    { size: "S", qty: s },
    { size: "M", qty: m },
    { size: "L", qty: l },
    { size: "XL", qty: xl },
    { size: "XXL", qty: xxl },
  ].filter((row) => row.qty > 0);

const sumQty = (rows: SpkSize[]) => rows.reduce((total, row) => total + row.qty, 0);

export const inProgressSpks: Spk[] = [
  {
    id: 101,
    spkNumber: "SPK/09/2026/0041",
    customerName: "PT Sinergi Busana",
    orderName: "Jaket Hoodie Komunitas Motor",
    material: "Fleece 320gsm",
    entryDate: "2026-09-08",
    deliveryDate: "2026-09-20",
    sizes: sizes(20, 60, 70, 30, 10),
    totalQty: sumQty(sizes(20, 60, 70, 30, 10)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: false,
    isDeliveryDone: false,
  },
  {
    id: 102,
    spkNumber: "SPK/09/2026/0042",
    customerName: "CV Mitra Garmen Sejahtera",
    orderName: "Kaos Polo Custom Kantor",
    material: "Lacoste CVC",
    entryDate: "2026-09-10",
    deliveryDate: "2026-09-25",
    sizes: sizes(15, 45, 50, 20),
    totalQty: sumQty(sizes(15, 45, 50, 20)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: true,
    isDeliveryDone: false,
  },
  {
    id: 103,
    spkNumber: "SPK/09/2026/0043",
    customerName: "SMA Negeri 3 Bandung",
    orderName: "Seragam Olahraga Angkatan 2026",
    material: "Diadora Micro",
    entryDate: "2026-09-05",
    deliveryDate: "2026-09-28",
    sizes: sizes(40, 90, 80, 40, 20),
    totalQty: sumQty(sizes(40, 90, 80, 40, 20)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: true,
    isPrintDone: false,
    isPressDone: false,
    isDeliveryDone: false,
  },
  {
    id: 104,
    spkNumber: "SPK/09/2026/0044",
    customerName: "Distro Kece Store",
    orderName: "Kaos Oversize Drop Ketiga",
    material: "Cotton Combed 24s",
    entryDate: "2026-09-12",
    deliveryDate: "2026-09-30",
    sizes: sizes(30, 80, 90, 40),
    totalQty: sumQty(sizes(30, 80, 90, 40)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: false,
    isDeliveryDone: false,
  },
  {
    id: 105,
    spkNumber: "SPK/09/2026/0045",
    customerName: "PT Sportindo Utama",
    orderName: "Jersey Futsal Turnamen Korporat",
    material: "Interlock Dryfit",
    entryDate: "2026-09-14",
    deliveryDate: "2026-10-01",
    sizes: sizes(10, 30, 35, 15),
    totalQty: sumQty(sizes(10, 30, 35, 15)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: true,
    isPrintDone: false,
    isPressDone: false,
    isDeliveryDone: false,
  },
  {
    id: 106,
    spkNumber: "SPK/09/2026/0046",
    customerName: "CV Anggun Fashion",
    orderName: "Gamis Seragam Majelis Taklim",
    material: "Kastina Premium",
    entryDate: "2026-09-15",
    deliveryDate: "2026-10-03",
    sizes: sizes(25, 55, 60, 25),
    totalQty: sumQty(sizes(25, 55, 60, 25)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: false,
    isPrintDone: false,
    isPressDone: false,
    isDeliveryDone: false,
  },
  {
    id: 107,
    spkNumber: "SPK/09/2026/0047",
    customerName: "UD Konveksi Jaya Abadi",
    orderName: "Rompi Proyek PT Bangun Karya",
    material: "Parasut Waterproof",
    entryDate: "2026-09-11",
    deliveryDate: "2026-09-24",
    sizes: sizes(10, 25, 30, 15),
    totalQty: sumQty(sizes(10, 25, 30, 15)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: true,
    isDeliveryDone: false,
  },
  {
    id: 108,
    spkNumber: "SPK/09/2026/0048",
    customerName: "PT Retail Fashion Indonesia",
    orderName: "Kemeja Seragam Staff Toko",
    material: "Twill Import",
    entryDate: "2026-09-16",
    deliveryDate: "2026-10-08",
    sizes: sizes(20, 50, 55, 25),
    totalQty: sumQty(sizes(20, 50, 55, 25)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "In Progress",
    isDesignDone: false,
    isPrintDone: false,
    isPressDone: false,
    isDeliveryDone: false,
  },
];

export const closedNeedingPriceSpks: Spk[] = [
  {
    id: 201,
    spkNumber: "SPK/09/2026/0031",
    customerName: "CV Baju Anak Ceria",
    orderName: "Setelan Piyama Anak Grosir",
    material: "Cotton Combed 30s",
    entryDate: "2026-08-20",
    deliveryDate: "2026-09-05",
    closedDate: "2026-09-04",
    sizes: sizes(30, 40, 30, 0),
    totalQty: sumQty(sizes(30, 40, 30, 0)),
    totalMeter: 210,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "Closed",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: true,
    isDeliveryDone: true,
  },
  {
    id: 202,
    spkNumber: "SPK/09/2026/0033",
    customerName: "PT Grosir Pakaian Nusantara",
    orderName: "Kaos Polos Stock Lokal",
    material: "Cotton Combed 20s",
    entryDate: "2026-08-22",
    deliveryDate: "2026-09-08",
    closedDate: "2026-09-07",
    sizes: sizes(50, 120, 130, 60, 20),
    totalQty: sumQty(sizes(50, 120, 130, 60, 20)),
    totalMeter: 495,
    pricePerMeter: null,
    hargaPerPiece: null,
    status: "Closed",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: true,
    isDeliveryDone: true,
  },
];

export const closedReadyForInvoiceSpks: Spk[] = [
  {
    id: 301,
    spkNumber: "SPK/09/2026/0021",
    customerName: "PT Sinergi Busana",
    orderName: "Kaos Event Ulang Tahun Perusahaan",
    material: "Cotton Combed 24s",
    entryDate: "2026-08-10",
    deliveryDate: "2026-08-25",
    closedDate: "2026-08-24",
    sizes: sizes(20, 60, 65, 25),
    totalQty: sumQty(sizes(20, 60, 65, 25)),
    totalMeter: 340,
    pricePerMeter: 48000,
    hargaPerPiece: null,
    status: "Closed",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: true,
    isDeliveryDone: true,
  },
  {
    id: 302,
    spkNumber: "SPK/09/2026/0023",
    customerName: "Distro Kece Store",
    orderName: "Kaos Oversize Drop Kedua",
    material: "Cotton Combed 24s",
    entryDate: "2026-08-12",
    deliveryDate: "2026-08-28",
    closedDate: "2026-08-27",
    sizes: sizes(25, 70, 75, 30),
    totalQty: sumQty(sizes(25, 70, 75, 30)),
    totalMeter: 0,
    pricePerMeter: null,
    hargaPerPiece: 62000,
    status: "Closed",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: true,
    isDeliveryDone: true,
  },
  {
    id: 303,
    spkNumber: "SPK/09/2026/0025",
    customerName: "SMA Negeri 3 Bandung",
    orderName: "Seragam Olahraga Angkatan 2025",
    material: "Diadora Micro",
    entryDate: "2026-08-15",
    deliveryDate: "2026-09-01",
    closedDate: "2026-08-31",
    sizes: sizes(35, 85, 75, 35, 15),
    totalQty: sumQty(sizes(35, 85, 75, 35, 15)),
    totalMeter: 415,
    pricePerMeter: 45000,
    hargaPerPiece: 58000,
    status: "Closed",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: true,
    isDeliveryDone: true,
  },
];

export const rejectedSpks: Spk[] = [
  {
    id: 401,
    spkNumber: "SPK/09/2026/0012",
    customerName: "Toko Baju Makmur",
    orderName: "Kemeja Batik Seragam Kantor",
    material: "Katun Batik Printing",
    entryDate: "2026-08-01",
    deliveryDate: "2026-08-14",
    closedDate: "2026-08-13",
    sizes: sizes(10, 25, 20, 10),
    totalQty: sumQty(sizes(10, 25, 20, 10)),
    totalMeter: 130,
    pricePerMeter: 42000,
    hargaPerPiece: null,
    status: "Rejected",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: false,
    isDeliveryDone: false,
  },
  {
    id: 402,
    spkNumber: "SPK/09/2026/0016",
    customerName: "CV Mitra Garmen Sejahtera",
    orderName: "Training Pack Komunitas Lari",
    material: "Baby Terry",
    entryDate: "2026-08-05",
    deliveryDate: "2026-08-19",
    closedDate: "2026-08-18",
    sizes: sizes(15, 30, 25, 10),
    totalQty: sumQty(sizes(15, 30, 25, 10)),
    totalMeter: 96,
    pricePerMeter: 51000,
    hargaPerPiece: null,
    status: "Rejected",
    isDesignDone: true,
    isPrintDone: true,
    isPressDone: false,
    isDeliveryDone: false,
  },
];

export const unpaidInvoices: Invoice[] = [
  {
    id: 5001,
    invoiceNumber: "INV/MTR/08/2026/0014",
    type: "MTR",
    spkId: 301,
    customerName: "PT Sinergi Busana",
    orderName: "Kaos Event Ulang Tahun Perusahaan (MTR)",
    totalQty: 170,
    totalAmount: 340 * 48000,
    paidAmount: 6_000_000,
    isPaid: false,
    issuedDate: "2026-08-25",
    payments: [{ date: "2026-09-01", amount: 6_000_000 }],
  },
  {
    id: 5002,
    invoiceNumber: "INV/QTY/08/2026/0011",
    type: "QTY",
    spkId: 302,
    customerName: "Distro Kece Store",
    orderName: "Kaos Oversize Drop Kedua (QTY)",
    totalQty: 200,
    totalAmount: 200 * 62000,
    paidAmount: 0,
    isPaid: false,
    issuedDate: "2026-08-28",
    payments: [],
  },
  {
    id: 5003,
    invoiceNumber: "INV/MTR/09/2026/0003",
    type: "MTR",
    spkId: 303,
    customerName: "SMA Negeri 3 Bandung",
    orderName: "Seragam Olahraga Angkatan 2025 (MTR)",
    totalQty: 245,
    totalAmount: 415 * 45000,
    paidAmount: 10_000_000,
    isPaid: false,
    issuedDate: "2026-09-01",
    payments: [{ date: "2026-09-05", amount: 10_000_000 }],
  },
  {
    id: 5004,
    invoiceNumber: "INV/QTY/09/2026/0004",
    type: "QTY",
    spkId: 303,
    customerName: "SMA Negeri 3 Bandung",
    orderName: "Seragam Olahraga Angkatan 2025 (QTY)",
    totalQty: 245,
    totalAmount: 245 * 58000,
    paidAmount: 0,
    isPaid: false,
    issuedDate: "2026-09-01",
    payments: [],
  },
];

export const paidInvoices: Invoice[] = [
  {
    id: 4001,
    invoiceNumber: "INV/MTR/07/2026/0032",
    type: "MTR",
    spkId: 291,
    customerName: "PT Grosir Pakaian Nusantara",
    orderName: "Kaos Polos Stock Lokal Juli (MTR)",
    totalQty: 380,
    totalAmount: 452 * 47000,
    paidAmount: 452 * 47000,
    isPaid: true,
    issuedDate: "2026-07-10",
    payments: [{ date: "2026-07-18", amount: 452 * 47000 }],
  },
  {
    id: 4002,
    invoiceNumber: "INV/QTY/07/2026/0029",
    type: "QTY",
    spkId: 292,
    customerName: "UD Konveksi Jaya Abadi",
    orderName: "Rompi Proyek Batch Juni (QTY)",
    totalQty: 120,
    totalAmount: 120 * 55000,
    paidAmount: 120 * 55000,
    isPaid: true,
    issuedDate: "2026-07-14",
    payments: [{ date: "2026-07-20", amount: 120 * 55000 }],
  },
  {
    id: 4003,
    invoiceNumber: "INV/MTR/08/2026/0009",
    type: "MTR",
    spkId: 293,
    customerName: "CV Anggun Fashion",
    orderName: "Gamis Seragam Batch Agustus (MTR)",
    totalQty: 210,
    totalAmount: 300 * 50000,
    paidAmount: 300 * 50000,
    isPaid: true,
    issuedDate: "2026-08-05",
    payments: [{ date: "2026-08-12", amount: 300 * 50000 }],
  },
];

// 12-month trend, ending at the demo month (Sep 2026)
export const omzetTrend = [
  { month: "Okt 2025", revenue: 148_500_000 },
  { month: "Nov 2025", revenue: 162_300_000 },
  { month: "Des 2025", revenue: 205_800_000 },
  { month: "Jan 2026", revenue: 131_200_000 },
  { month: "Feb 2026", revenue: 158_900_000 },
  { month: "Mar 2026", revenue: 176_400_000 },
  { month: "Apr 2026", revenue: 169_700_000 },
  { month: "Mei 2026", revenue: 184_200_000 },
  { month: "Jun 2026", revenue: 198_600_000 },
  { month: "Jul 2026", revenue: 212_900_000 },
  { month: "Agu 2026", revenue: 227_100_000 },
  { month: "Sep 2026", revenue: 143_800_000 },
];

export const rejectTrend = [
  { month: "Okt 2025", rejected: 3 },
  { month: "Nov 2025", rejected: 2 },
  { month: "Des 2025", rejected: 4 },
  { month: "Jan 2026", rejected: 5 },
  { month: "Feb 2026", rejected: 2 },
  { month: "Mar 2026", rejected: 3 },
  { month: "Apr 2026", rejected: 1 },
  { month: "Mei 2026", rejected: 3 },
  { month: "Jun 2026", rejected: 2 },
  { month: "Jul 2026", rejected: 4 },
  { month: "Agu 2026", rejected: 2 },
  { month: "Sep 2026", rejected: 2 },
];

export function progressPercentage(spk: Spk): number {
  const stages = [spk.isDesignDone, spk.isPrintDone, spk.isPressDone, spk.isDeliveryDone];
  const done = stages.filter(Boolean).length;
  return Math.round((done / stages.length) * 100);
}

export type UrgencyTone = "critical" | "warning" | "good" | "info";

export function urgencyTone(daysLeft: number): UrgencyTone {
  if (daysLeft <= 8) return "critical";
  if (daysLeft <= 10) return "warning";
  if (daysLeft <= 12) return "good";
  return "info";
}
