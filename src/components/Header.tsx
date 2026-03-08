"use client";

import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import Link from "next/link";

export default function Header() {
    const [scrolled, setScrolled] = useState(false);
    const [user, setUser] = useState<any>(null);

    const [isMenuOpen, setIsMenuOpen] = useState(false);

    const [applicantToken, setApplicantToken] = useState<string | null>(null);

    useEffect(() => {
        const storedUser = localStorage.getItem("user");
        if (storedUser) {
            try {
                setUser(JSON.parse(storedUser));
            } catch (e) {
                console.error("Failed to parse user", e);
            }
        }
        const appToken = localStorage.getItem('applicant_token');
        setApplicantToken(appToken);

        const handleScroll = () => setScrolled(window.scrollY > 50);
        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, []);

    const handleLogout = () => {
        localStorage.removeItem("user");
        localStorage.removeItem("auth_token");
        setUser(null);
        window.location.href = "/";
    };

    return (
        <>
            <motion.header
                initial={{ y: -80, opacity: 0 }}
                animate={{ y: 0, opacity: 1 }}
                transition={{ duration: 0.6, ease: "easeOut" }}
                className={`sticky top-0 left-0 w-full z-50 transition-all duration-300 ${scrolled
                    ? "bg-white/95 backdrop-blur-md shadow-sm border-b border-[#000000]/5 py-4"
                    : "bg-white py-5"
                    }`}
            >
                <div className="max-w-7xl mx-auto px-6 md:px-8 flex justify-between items-center">
                    {/* Logo & Brand */}
                    <motion.div whileHover="hover" className="relative shrink-0">
                        <Link href="/" className="flex flex-col group relative">
                            <div className="flex items-center">
                                <motion.span
                                    variants={{ hover: { x: 2 } }}
                                    transition={{ duration: 0.3, ease: "easeOut" }}
                                    className="text-[#000000] font-black text-2xl md:text-3xl tracking-tight leading-none"
                                >
                                    DROGA
                                </motion.span>
                                <motion.span
                                    variants={{ hover: { x: 4 } }}
                                    transition={{ duration: 0.3, ease: "easeOut" }}
                                    className="text-[#000000]/60 font-medium text-2xl md:text-3xl tracking-tight ml-2 leading-none"
                                >
                                    GROUP
                                </motion.span>
                            </div>
                            <div className="flex items-center gap-2 mt-1">
                                <div className="h-[0.5px] w-4 bg-[#000000]/40" />
                                <span className="text-[9px] font-black uppercase tracking-[0.3em] text-[#000000]/60 whitespace-nowrap">
                                    Hiring Hub
                                </span>
                                <motion.div
                                    variants={{ hover: { scaleX: 1.1, originX: 0 } }}
                                    className="h-[0.5px] w-full bg-[#000000]/40 flex-1"
                                />
                            </div>
                        </Link>
                    </motion.div>

                    {/* Nav */}
                    <nav className="flex items-center gap-4 lg:gap-10">
                        <div className="hidden lg:flex items-center gap-10">
                            {["Jobs", "About Us", "Contact"].map((item, i) => (
                                <motion.a
                                    key={item}
                                    initial={{ opacity: 0, scale: 0.9 }}
                                    animate={{ opacity: 1, scale: 1 }}
                                    transition={{ duration: 0.5, delay: i * 0.1 }}
                                    href={`/${item.toLowerCase() === "about us" ? "#about-us" : item.toLowerCase() === "contact" ? "#contact" : "#" + item.toLowerCase()}`}
                                    className="text-[13px] font-black tracking-wider transition-all hover:text-black hover:bg-[#FDF22F] px-4 py-2 rounded-lg text-[#000000]/80"
                                >
                                    {item}
                                </motion.a>
                            ))}

                            {user ? (
                                <div className="flex items-center gap-6 pl-4 border-l border-[#000000]/10">
                                    <Link
                                        href="/dashboard"
                                        className="text-[13px] font-black uppercase tracking-wider text-[#000000] hover:text-[#000000] transition-colors"
                                    >
                                        Dashboard
                                    </Link>
                                    <button
                                        onClick={handleLogout}
                                        className="text-[13px] font-black uppercase tracking-wider text-red-500/80 hover:text-red-600 transition-colors"
                                    >
                                        Logout
                                    </button>
                                </div>
                            ) : (
                                <div className="flex items-center gap-3 pl-4 border-l border-[#000000]/10">
                                    {/* Premium Track Application CTA */}
                                    <Link
                                        href="/my-applications"
                                        className="group relative flex items-center gap-2.5 bg-[#FDF22F] hover:bg-black text-black hover:text-[#FDF22F] pl-2 pr-5 py-2 rounded-full font-black text-[11px] uppercase tracking-widest transition-all duration-300 shadow-lg shadow-[#FDF22F]/40 hover:shadow-black/20 hover:-translate-y-0.5 active:scale-95"
                                    >
                                        {/* Avatar circle */}
                                        <div className="relative w-7 h-7 rounded-full bg-black group-hover:bg-[#FDF22F] flex items-center justify-center shrink-0 transition-colors duration-300">
                                            {applicantToken ? (
                                                <span className="text-[#FDF22F] group-hover:text-black font-black text-[11px] transition-colors duration-300">●</span>
                                            ) : (
                                                <svg className="w-3.5 h-3.5 text-[#FDF22F] group-hover:text-black transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" /></svg>
                                            )}
                                            {/* Pulsing green live dot */}
                                            <span className="absolute -top-0.5 -right-0.5 flex h-2.5 w-2.5">
                                                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 border-2 border-[#FDF22F] group-hover:border-black transition-colors duration-300"></span>
                                            </span>
                                        </div>
                                        <span className="whitespace-nowrap">
                                            {applicantToken ? 'My Applications' : 'Track Application'}
                                        </span>
                                        <svg className="w-3 h-3 opacity-60 group-hover:translate-x-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M9 5l7 7-7 7" /></svg>
                                    </Link>

                                    <Link
                                        href="/login"
                                        className="text-[12px] font-bold tracking-wider text-[#000000]/50 hover:text-black transition-colors"
                                    >
                                        Staff Login
                                    </Link>
                                </div>
                            )}
                        </div>

                        <motion.a
                            initial={{ opacity: 0, x: 20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.5, delay: 0.5 }}
                            href="#jobs"
                            className="bg-[#000000] text-white text-[10px] md:text-[11px] font-black uppercase tracking-[0.1em] px-6 md:px-10 py-3 md:py-4 rounded-full hover:bg-black transition-all hover:shadow-2xl hover:shadow-[#000000]/20 whitespace-nowrap"
                        >
                            View Positions →
                        </motion.a>

                        {/* Hamburger Button */}
                        <button
                            onClick={() => setIsMenuOpen(!isMenuOpen)}
                            className="lg:hidden w-10 h-10 flex items-center justify-center bg-[#000000]/5 rounded-full z-[60]"
                        >
                            <div className="flex flex-col gap-1 w-5">
                                <motion.div
                                    animate={isMenuOpen ? { rotate: 45, y: 5 } : { rotate: 0, y: 0 }}
                                    className="h-0.5 w-full bg-[#000000] rounded-full"
                                />
                                <motion.div
                                    animate={isMenuOpen ? { opacity: 0, x: -10 } : { opacity: 1, x: 0 }}
                                    className="h-0.5 w-full bg-[#000000] rounded-full"
                                />
                                <motion.div
                                    animate={isMenuOpen ? { rotate: -45, y: -5 } : { rotate: 0, y: 0 }}
                                    className="h-0.5 w-full bg-[#000000] rounded-full"
                                />
                            </div>
                        </button>
                    </nav>
                </div>
            </motion.header>

            {/* Mobile Sidebar Overlay */}
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: isMenuOpen ? 1 : 0 }}
                style={{ pointerEvents: isMenuOpen ? "auto" : "none" }}
                onClick={() => setIsMenuOpen(false)}
                className="fixed inset-0 bg-[#000000]/20 backdrop-blur-sm z-50 lg:hidden"
            />

            {/* Mobile Sidebar */}
            <motion.div
                initial={{ x: "100%" }}
                animate={{ x: isMenuOpen ? "0%" : "100%" }}
                transition={{ type: "spring", damping: 25, stiffness: 200 }}
                className="fixed top-0 right-0 h-full w-[80%] max-w-[320px] bg-white z-[55] lg:hidden shadow-2xl p-8 flex flex-col"
            >
                <div className="flex justify-between items-start mb-10">
                    <div className="flex flex-col">
                        <span className="text-[#000000] font-black text-2xl tracking-tighter leading-none">DROGA GROUP</span>
                        <span className="text-[10px] font-bold uppercase tracking-[0.2em] text-[#000000]/30 mt-1">Hiring Hub</span>
                    </div>
                </div>

                <div className="flex flex-col gap-4">
                    {["Jobs", "About Us", "Contact"].map((item, i) => (
                        <motion.a
                            key={item}
                            initial={{ opacity: 0, x: 20 }}
                            animate={{ opacity: isMenuOpen ? 1 : 0, x: isMenuOpen ? 0 : 20 }}
                            transition={{ delay: i * 0.1 + 0.2 }}
                            href={`/${item.toLowerCase() === "about us" ? "#about-us" : item.toLowerCase() === "contact" ? "#contact" : "#" + item.toLowerCase()}`}
                            onClick={() => setIsMenuOpen(false)}
                            className="text-3xl font-bold text-[#000000] tracking-tighter hover:bg-[#FDF22F] -mx-4 px-4 py-3 rounded-xl transition-colors"
                        >
                            {item}
                        </motion.a>
                    ))}

                    <div className="h-px w-full bg-[#000000]/5 my-4" />

                    {user ? (
                        <>
                            <Link
                                href="/dashboard"
                                onClick={() => setIsMenuOpen(false)}
                                className="text-xl font-bold text-[#000000]"
                            >
                                Dashboard
                            </Link>
                            <button
                                onClick={handleLogout}
                                className="text-xl font-bold text-red-500 text-left"
                            >
                                Logout
                            </button>
                        </>
                    ) : (
                        <>
                            {/* Always visible applicant portal link */}
                            <Link
                                href="/my-applications"
                                onClick={() => setIsMenuOpen(false)}
                                className="flex items-center gap-3 text-xl font-bold text-[#000000] tracking-tighter hover:bg-[#FDF22F] -mx-4 px-4 py-3 rounded-xl transition-colors"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                {applicantToken ? 'My Applications' : 'Track Application'}
                            </Link>
                            <Link
                                href="/login"
                                onClick={() => setIsMenuOpen(false)}
                                className="text-2xl font-bold text-[#000000]/40 tracking-tighter"
                            >
                                Staff Login
                            </Link>
                        </>
                    )}
                </div>

                <div className="mt-auto flex flex-col items-center">
                    {/* Thumb Zone Close Button */}
                    <button
                        onClick={() => setIsMenuOpen(false)}
                        className="w-16 h-16 flex items-center justify-center bg-[#FDF22F] text-black rounded-full shadow-xl hover:shadow-2xl transition-all active:scale-90 mb-8"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>

                    <div className="w-full pt-6 border-t border-[#000000]/5 flex justify-between items-center text-[10px] font-bold text-[#000000]/20 uppercase tracking-[0.2em]">
                        <span>Droga Group Hiring Hub</span>
                        <span>© 2026</span>
                    </div>
                </div>
            </motion.div>
        </>
    );
}
