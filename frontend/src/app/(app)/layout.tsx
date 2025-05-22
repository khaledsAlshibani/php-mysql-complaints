import { Navbar } from "@/components/Navbar"

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <div className="min-h-screen">
      <Navbar />
      <main className="container max-w-screen-lg mx-auto">
        {children}
      </main>
    </div>
  );
}
