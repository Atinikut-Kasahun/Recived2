'use client';

import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { API_URL } from '@/lib/api';
import Link from 'next/link';

export default function ForgotPasswordPage() {
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);
    const [sent, setSent] = useState(false);
    const [error, setError] = useState('');

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        try {
            const cleanBaseUrl = API_URL.endsWith('/') ? API_URL.slice(0, -1) : API_URL;
            const res = await fetch(`${cleanBaseUrl}/v1/applicant/forgot-password`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email }),
            });
            // Always show success — never reveal if email exists or not
            setSent(true);
        } catch {
            setError('Network error. Please check your connection and try again.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-[#F5F6FA] flex items-center justify-center px-6">

            {/* Background blobs */}
            <div className="fixed top-0 right-0 w-[600px] h-[600px] bg-[#FDF22F]/5 rounded-full blur-[120px] pointer-events-none" />
            <div className="fixed bottom-0 left-0 w-[400px] h-[400px] bg-blue-50/10 rounded-full blur-[100px] pointer-events-none" />

            <div className="w-full max-w-md relative z-10">

                {/* Brand Logo Header */}
                <motion.div
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                    className="flex justify-center mb-8"
                >
                    <Link href="/my-applications" className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-[#FDF22F] rounded-lg flex items-center justify-center shadow-sm">
                            <svg className="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span className="text-[12px] font-black uppercase tracking-[0.1em] text-gray-500">Droga Hiring Hub</span>
                    </Link>
                </motion.div>

                <AnimatePresence mode="wait">

                    {/* ── FORM STATE ── */}
                    {!sent && (
                        <motion.div
                            key="form"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -20 }}
                            transition={{ duration: 0.4 }}
                            className="bg-white rounded-[40px] shadow-2xl shadow-black/5 border border-gray-100 overflow-hidden"
                        >
                            <div className="bg-[#FDF22F] px-10 pt-14 pb-12 rounded-b-[40px]">
                                <div className="w-16 h-16 bg-black rounded-2xl flex items-center justify-center mb-8 shadow-sm">
                                    <svg className="w-8 h-8 text-[#FDF22F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <h1 className="text-[32px] font-black text-black tracking-tight leading-none mb-3">Forgot your password?</h1>
                                <p className="text-[15px] text-black/60 font-medium leading-relaxed">
                                    No worries. Enter your registered email address and we'll send you a secure link to reset it.
                                </p>
                            </div>

                            {/* Card Body */}
                            <div className="px-10 py-10">
                                <form onSubmit={handleSubmit} className="space-y-6">
                                    <div>
                                        <label className="block text-[11px] font-black text-gray-400 uppercase tracking-[0.05em] mb-2.5">
                                            Email Address
                                        </label>
                                        <input
                                            type="email"
                                            required
                                            placeholder="e.g. john@example.com"
                                            value={email}
                                            onChange={e => setEmail(e.target.value)}
                                            className="w-full px-5 py-4 bg-transparent border border-gray-200 rounded-[14px] outline-none focus:border-[#FDF22F] hover:border-gray-300 transition-colors font-medium text-gray-900 text-[15px] placeholder:text-gray-400"
                                        />
                                    </div>

                                    {error && (
                                        <motion.div
                                            initial={{ opacity: 0, y: -5 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            className="flex items-center gap-3 bg-red-50 text-red-600 rounded-xl px-4 py-3"
                                        >
                                            <p className="text-sm font-medium">{error}</p>
                                        </motion.div>
                                    )}

                                    <button
                                        type="submit"
                                        disabled={loading || !email}
                                        className="w-full py-4 bg-[#FDF22F] text-black rounded-[14px] font-black text-[13px] uppercase tracking-widest hover:bg-[#FDF22F]/90 transition-all disabled:opacity-50 mt-2"
                                    >
                                        {loading ? (
                                            <span className="flex items-center justify-center gap-2">
                                                <div className="w-4 h-4 border-2 border-black/20 border-t-black rounded-full animate-spin" />
                                                Sending...
                                            </span>
                                        ) : 'Send Reset Link →'}
                                    </button>
                                </form>

                                <div className="mt-8 pt-8 text-center border-t border-gray-50/50">
                                    <Link
                                        href="/my-applications"
                                        className="inline-flex items-center gap-2 text-[11px] font-black text-gray-400 hover:text-black transition-colors uppercase tracking-widest"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        Back to Login
                                    </Link>
                                </div>
                            </div>
                        </motion.div>
                    )}

                    {/* ── SUCCESS STATE ── */}
                    {sent && (
                        <motion.div
                            key="success"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.4 }}
                            className="bg-white rounded-[40px] shadow-2xl shadow-black/5 overflow-hidden"
                        >
                            {/* Success Header */}
                            <div className="bg-[#FDF22F] px-10 pt-16 pb-12 rounded-b-[40px] text-center">
                                <motion.div
                                    initial={{ scale: 0 }}
                                    animate={{ scale: 1 }}
                                    transition={{ delay: 0.2, type: "spring", stiffness: 200 }}
                                    className="w-16 h-16 bg-black rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-sm"
                                >
                                    <svg className="w-8 h-8 text-[#FDF22F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </motion.div>
                                <h2 className="text-[28px] font-black text-black tracking-tight leading-none mb-4">Check your inbox</h2>
                                <p className="text-[15px] text-black/60 font-medium leading-relaxed max-w-sm mx-auto">
                                    We've sent a password reset link to <span className="text-black font-bold">{email}</span>
                                </p>
                            </div>

                            {/* Success Body */}
                            <div className="px-10 py-10 space-y-6">

                                {/* Steps */}
                                <div className="space-y-4">
                                    {[
                                        { step: '01', text: 'Open the email from Droga Hiring Hub' },
                                        { step: '02', text: 'Click the "Reset My Password" button' },
                                        { step: '03', text: 'Create your new password and sign in' },
                                    ].map(({ step, text }) => (
                                        <div key={step} className="flex items-center gap-4 p-4 bg-gray-50/50 rounded-2xl border border-gray-100/50">
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest shrink-0 w-6">{step}</span>
                                            <p className="text-sm font-bold text-gray-700">{text}</p>
                                        </div>
                                    ))}
                                </div>

                                {/* Warning */}
                                <div className="flex items-start gap-3 bg-[#FDF22F]/10 border border-[#FDF22F]/30 rounded-2xl p-5">
                                    <svg className="w-5 h-5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p className="text-[13px] font-medium text-gray-600 leading-relaxed">
                                        The link expires in <span className="font-bold text-black">60 minutes</span>. If you don't see the email, check your spam or junk folder.
                                    </p>
                                </div>

                                {/* Resend + Back */}
                                <div className="pt-6 border-t border-gray-50/50 flex flex-col items-center gap-4">
                                    <button
                                        onClick={() => { setSent(false); setEmail(''); }}
                                        className="text-[11px] font-black text-gray-500 hover:text-black transition-colors uppercase tracking-widest"
                                    >
                                        Try a different email address
                                    </button>
                                    <Link
                                        href="/my-applications"
                                        className="inline-flex items-center gap-2 text-[11px] font-black text-gray-400 hover:text-black transition-colors uppercase tracking-widest"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        Back to Login
                                    </Link>
                                </div>
                            </div>
                        </motion.div>
                    )}

                </AnimatePresence>
            </div>
        </div>
    );
}
