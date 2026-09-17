import { Head, Link } from '@inertiajs/react';
import {
    AlertCircle,
    ArrowLeft,
    Check,
    CheckCircle2,
    ChevronDown,
    ChevronUp,
    Code2,
    Copy,
    Download,
    Eye,
    FileJson,
    Info,
    LayoutGrid,
    ListPlus,
    Loader2,
    Plus,
    RotateCcw,
    Search,
    Sparkles,
    Table as TableIcon,
    Trash2,
    XCircle,
} from 'lucide-react';
import React, { useMemo, useState } from 'react';

type CardRow = {
    id: number;
    value: string;
};

type ResultItem = {
    input: number | string;
    result: Record<string, unknown> & { not_found?: boolean; message?: string; requested_card?: unknown };
};

// Diccionario amigable para nombres técnicos de tarjetas del Sistema MIO / Metro Cali
const FIELD_DEFINITIONS: Record<string, { label: string; description?: string }> = {
    crd_intsnr: { label: 'N° Interno de Tarjeta', description: 'Identificador único interno en el sistema MIO' },
    crd_snr: { label: 'N° de Serie / SNR', description: 'Número de serie impreso en la tarjeta física' },
    crd_status: { label: 'Estado Operativo', description: 'Estado actual de la tarjeta en la red Metro Cali' },
    crd_crdpan: { label: 'PAN / N° Enmascarado', description: 'Número de tarjeta / cuenta principal' },
    iss_id: { label: 'ID del Emisor', description: 'Entidad emisora / operador Metro Cali' },
    cd_id: { label: 'ID Centro de Distribución', description: 'Estación, terminal o punto de personalización' },
    dc_code: { label: 'Código DC', description: 'Código del punto de entrega MIO' },
    cty_id: { label: 'Tipo de Tarjeta (CTY)', description: 'Categoría tarifaria (Común, Estudiantil, Adulto Mayor, etc.)' },
    crd_chkdg: { label: 'Dígito Verificador', description: 'Código de chequeo de seguridad' },
    crd_certificate: { label: 'Certificado SAM/Seguridad', description: 'Certificado criptográfico de acceso' },
    crd_regdate: { label: 'Fecha de Expedición / Registro', description: 'Fecha de alta en el sistema Metro Cali' },
    crd_reguser: { label: 'Usuario Operador', description: 'Funcionario que registró o emitió la tarjeta' },
    crd_secondcopytax: { label: 'Tasa Duplicado', description: 'Valor aplicado por reposición' },
    is_personalized: { label: 'Personalizada (Nominativa)', description: 'Registrada a nombre de un ciudadano' },
    user_id: { label: 'ID Usuario Titular', description: 'Identificación del ciudadano registrado' },
    not_found: { label: 'No Encontrada', description: 'Indica si no existe en la base de datos' },
    message: { label: 'Mensaje del Sistema', description: 'Detalle o diagnóstico de la respuesta' },
    requested_card: { label: 'Tarjeta Solicitada', description: 'Valor consultado en la petición' },
};

function getFieldLabel(key: string): string {
    if (FIELD_DEFINITIONS[key]?.label) {
        return FIELD_DEFINITIONS[key].label;
    }
    return key.replace(/_/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
}

function renderFormattedValue(key: string, value: unknown): React.ReactNode {
    if (value === null || value === undefined || value === '') {
        return <span className="font-mono text-xs text-slate-400 italic">Sin información</span>;
    }

    if (typeof value === 'boolean') {
        return (
            <span
                className={`inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-semibold ${
                    value ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-[#E30613]'
                }`}
            >
                {value ? 'Sí' : 'No'}
            </span>
        );
    }

    if (key === 'crd_status' || key === 'status') {
        const strVal = String(value).toUpperCase();
        let badgeStyle = 'bg-slate-100 text-slate-700 border-slate-200';
        let label = String(value);

        if (strVal === 'A' || strVal === 'ACTIVA' || strVal === 'ACTIVE') {
            badgeStyle = 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold';
            label = 'Activa (' + strVal + ')';
        } else if (strVal === 'I' || strVal === 'INACTIVA' || strVal === 'INACTIVE') {
            badgeStyle = 'bg-amber-50 text-amber-800 border-amber-200 font-semibold';
            label = 'Inactiva (' + strVal + ')';
        } else if (strVal === 'B' || strVal === 'BLOQUEADA' || strVal === 'BLOCKED') {
            badgeStyle = 'bg-red-50 text-[#E30613] border-red-200 font-bold';
            label = 'Bloqueada (' + strVal + ')';
        }

        return (
            <span className={`inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs ${badgeStyle}`}>
                <span className="h-1.5 w-1.5 rounded-full bg-current" />
                {label}
            </span>
        );
    }

    if (key === 'is_personalized') {
        const isTrue = value === 1 || value === '1' || value === true || String(value).toLowerCase() === 'true';
        return (
            <span
                className={`inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-semibold ${
                    isTrue ? 'border-blue-200 bg-blue-50 text-[#003865]' : 'border-slate-200 bg-slate-100 text-slate-700'
                }`}
            >
                {isTrue ? 'Nominativa (Personalizada)' : 'Al portador'}
            </span>
        );
    }

    if (typeof value === 'object') {
        return (
            <pre className="max-w-md overflow-x-auto rounded-lg border border-slate-300 bg-slate-900 p-2 font-mono text-[11px] text-blue-200">
                {JSON.stringify(value, null, 2)}
            </pre>
        );
    }

    return <span className="font-mono text-xs font-medium text-slate-800">{String(value)}</span>;
}

export default function ApiTester() {
    const [rows, setRows] = useState<CardRow[]>([
        { id: 1, value: '101' },

    ]);
    const [bulkInput, setBulkInput] = useState('');
    const [showBulkMode, setShowBulkMode] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [mappedResults, setMappedResults] = useState<ResultItem[]>([]);
    const [apiResponse, setApiResponse] = useState<Record<string, unknown> | null>(null);
    const [expandedCardIndex, setExpandedCardIndex] = useState<number | null>(null);
    const [activeTab, setActiveTab] = useState<'table' | 'cards' | 'json'>('table');
    const [filterQuery, setFilterQuery] = useState('');
    const [statusFilter, setStatusFilter] = useState<'all' | 'found' | 'not_found'>('all');
    const [copiedGlobal, setCopiedGlobal] = useState(false);
    const [copiedRowId, setCopiedRowId] = useState<string | number | null>(null);
    const [executionTimeMs, setExecutionTimeMs] = useState<number | null>(null);

    const addRow = () => {
        setRows((current) => [...current, { id: Date.now() + Math.random(), value: '' }]);
    };

    const removeRow = (id: number) => {
        setRows((current) => {
            if (current.length === 1) {
                return [{ id: 1, value: '' }];
            }
            return current.filter((row) => row.id !== id);
        });
    };

    const handleChange = (id: number, value: string) => {
        setRows((current) => current.map((row) => (row.id === id ? { ...row, value } : row)));
    };

    const loadSampleData = () => {
        setRows([
            { id: 1, value: '101' },
            { id: 2, value: '102' },
            { id: 3, value: '205' },
        ]);
        setError(null);
    };

    const clearAll = () => {
        setRows([{ id: 1, value: '' }]);
        setMappedResults([]);
        setApiResponse(null);
        setError(null);
        setExecutionTimeMs(null);
    };

    const handleApplyBulk = () => {
        const tokens = bulkInput
            .split(/[\n,; ]+/)
            .map((t) => t.trim())
            .filter((t) => /^\d+$/.test(t));

        if (tokens.length === 0) {
            setError('No se encontraron números válidos en el texto ingresado.');
            return;
        }

        const uniqueTokens = Array.from(new Set(tokens));
        setRows(uniqueTokens.map((val, idx) => ({ id: idx + 1, value: val })));
        setShowBulkMode(false);
        setBulkInput('');
        setError(null);
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setLoading(true);
        setError(null);
        setMappedResults([]);
        setApiResponse(null);
        setExpandedCardIndex(null);

        const values = rows.map((row) => row.value.trim()).filter(Boolean);

        if (values.length === 0) {
            setError('Ingresa al menos un número de tarjeta del sistema MIO.');
            setLoading(false);
            return;
        }

        const startTime = performance.now();

        try {
            const response = await fetch('/api-tester-proxy', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ cards: values }),
            });

            const endTime = performance.now();
            setExecutionTimeMs(Math.round(endTime - startTime));

            const payload = await response.json();

            if (!response.ok) {
                setError(payload.message || 'Error al consultar la API de Metro Cali.');
                setApiResponse(payload);
                return;
            }

            const foundByInput: Record<string, Record<string, unknown>> = {};
            const data = Array.isArray(payload.data) ? payload.data : [];

            for (const item of data) {
                if (item?.crd_intsnr !== undefined && item?.crd_intsnr !== null) {
                    foundByInput[String(item.crd_intsnr)] = item as Record<string, unknown>;
                }
                if (item?.crd_snr !== undefined && item?.crd_snr !== null) {
                    foundByInput[String(item.crd_snr)] = item as Record<string, unknown>;
                }
                if (item?.requested_card !== undefined && item?.requested_card !== null) {
                    foundByInput[String(item.requested_card)] = item as Record<string, unknown>;
                }
            }

            const resultItems: ResultItem[] = values.map((value) => {
                const key = String(value);
                const foundItem =
                    foundByInput[key] ?? data.find((d: Record<string, unknown>) => String(d?.crd_intsnr) === key || String(d?.crd_snr) === key);

                return {
                    input: value,
                    result: (foundItem as Record<string, unknown> & { not_found?: boolean; message?: string }) ?? {
                        not_found: true,
                        message: 'No se encontró registro para este identificador en la base de datos de Metro Cali.',
                        requested_card: value,
                    },
                };
            });

            setMappedResults(resultItems);
            setApiResponse(payload);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'No se pudo establecer conexión con el servidor de Metro Cali.');
        } finally {
            setLoading(false);
        }
    };

    const handleCopyJson = async (dataToCopy: unknown, rowKey?: string | number) => {
        try {
            const jsonText = JSON.stringify(dataToCopy, null, 2);
            await navigator.clipboard.writeText(jsonText);

            if (rowKey !== undefined) {
                setCopiedRowId(rowKey);
                setTimeout(() => setCopiedRowId(null), 2200);
            } else {
                setCopiedGlobal(true);
                setTimeout(() => setCopiedGlobal(false), 2200);
            }
        } catch (err) {
            console.error('Error al copiar al portapapeles:', err);
        }
    };

    const handleDownloadJson = () => {
        const dataToSave = apiResponse ?? mappedResults;
        const blob = new Blob([JSON.stringify(dataToSave, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `metrocali-tarjetas-${new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-')}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    // Filtrar resultados
    const filteredResults = useMemo(() => {
        return mappedResults.filter((item) => {
            const isNotFound = Boolean(item.result?.not_found);
            if (statusFilter === 'found' && isNotFound) return false;
            if (statusFilter === 'not_found' && !isNotFound) return false;

            if (!filterQuery.trim()) return true;
            const query = filterQuery.toLowerCase();

            if (String(item.input).toLowerCase().includes(query)) return true;
            const jsonStr = JSON.stringify(item.result).toLowerCase();
            return jsonStr.includes(query);
        });
    }, [mappedResults, statusFilter, filterQuery]);

    // Estadísticas
    const totalFound = mappedResults.filter((r) => !r.result?.not_found).length;
    const totalNotFound = mappedResults.filter((r) => r.result?.not_found).length;

    return (
        <>
            <Head title="Metro Cali S.A. | Probador de API Tarjetas MIO" />

            {/* Franja institucional tricolor superior distintiva de Metro Cali y MIO */}
            <div className="fixed top-0 right-0 left-0 z-50 h-2 bg-gradient-to-r from-[#003865] via-[#005FA3] to-[#E30613] shadow-xs" />

            {/* Contenedor principal con fondo institucional claro representativo de Metrocali */}
            <div className="min-h-screen bg-[#F4F7FB] pt-2 text-slate-800 selection:bg-[#E30613]/20 selection:text-[#003865]">
                {/* Fondo sutil con suave degradado institucional azul Metro Cali */}
                <div className="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#E6EFF8]/60 via-[#F4F7FB] to-[#EBF1F7]" />

                <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {/* Barra de navegación superior institucional */}
                    <div className="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-sm">
                        <div className="flex items-center gap-3">
                            <Link
                                href="/"
                                className="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-[#003865] transition hover:border-[#005FA3] hover:bg-blue-50"
                            >
                                <ArrowLeft className="h-3.5 w-3.5 text-[#E30613]" />
                                Portal Principal
                            </Link>
                            <Link
                                href="/docs"
                                className="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-[#003865] transition hover:border-[#005FA3] hover:bg-blue-50"
                            >
                                <Info className="h-3.5 w-3.5 text-[#006BB8]" />
                                Documentación Swagger
                            </Link>
                        </div>

                        {/* Distintivo oficial de Metro Cali S.A. y Sistema MIO */}
                        <div className="flex items-center gap-3">
                            <div className="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 shadow-2xs">
                                <div className="flex h-6 w-9 items-center justify-center rounded-md bg-[#E30613] text-[11px] font-black tracking-wider text-white shadow-xs">
                                    MIO
                                </div>
                                <div className="flex flex-col leading-none">
                                    <span className="text-[11px] font-black tracking-wider text-[#003865] uppercase">METRO CALI S.A.</span>
                                    <span className="text-[9px] font-semibold text-slate-500">Entidad Pública Gestora</span>
                                </div>
                            </div>

                            <span className="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 shadow-2xs">
                                <span className="h-2 w-2 animate-pulse rounded-full bg-emerald-500" />
                                Gateway MIO Conectado
                            </span>
                        </div>
                    </div>

                    {/* Encabezado principal */}
                    <div className="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div className="mb-2 inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold text-[#004B87]">
                                <span className="h-2 w-2 rounded-full bg-[#E30613]" />
                                Plataforma de Tarjetas • Sistema Integrado de Transporte Masivo
                            </div>
                            <h1 className="flex items-center gap-3 text-3xl font-black tracking-tight text-[#002D54] sm:text-4xl">
                                <span>Consulta de Tarjetas MIO</span>
                                <span className="rounded-md bg-[#E30613] px-2.5 py-0.5 text-xs font-black tracking-widest text-white uppercase shadow-xs">
                                    API
                                </span>
                            </h1>
                            <p className="mt-1 max-w-2xl text-sm text-slate-600">
                                Módulo oficial de Metro Cali para la verificación y auditoría en tiempo real de tarjetas del Sistema MIO.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                onClick={loadSampleData}
                                className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-[#003865] shadow-xs transition hover:border-[#004B87] hover:bg-blue-50"
                            >
                                <Sparkles className="h-3.5 w-3.5 text-[#E30613]" />
                                Cargar ejemplos
                            </button>
                            <button
                                type="button"
                                onClick={clearAll}
                                className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs transition hover:border-red-200 hover:bg-red-50 hover:text-[#E30613]"
                            >
                                <RotateCcw className="h-3.5 w-3.5" />
                                Limpiar todo
                            </button>
                        </div>
                    </div>

                    {/* Tarjeta de Formulario de Consulta (Fondo Blanco Claro Institucional) */}
                    <div className="mb-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                        <div className="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                            <div className="flex items-center gap-2.5">
                                <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-[#003865] text-xs font-black text-white shadow-xs">
                                    1
                                </span>
                                <div>
                                    <h2 className="text-base font-bold text-[#002D54]">Números Internos de Tarjetas MIO</h2>
                                    <p className="text-xs text-slate-500">
                                        Ingresa los identificadores de tarjeta que deseas verificar en la base de datos de Metro Cali.
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                onClick={() => setShowBulkMode(!showBulkMode)}
                                className="inline-flex items-center gap-1.5 text-xs font-bold text-[#004B87] transition hover:text-[#006BB8]"
                            >
                                <ListPlus className="h-3.5 w-3.5 text-[#E30613]" />
                                {showBulkMode ? 'Volver a campos individuales' : 'Pegar lista masiva (separada por comas)'}
                            </button>
                        </div>

                        {showBulkMode ? (
                            <div className="mb-4 space-y-3 rounded-xl border border-blue-100 bg-[#F5F9FD] p-4">
                                <label className="block text-xs font-bold text-[#003865]">
                                    Ingresa varios números de tarjeta separados por comas, espacios o saltos de línea:
                                </label>
                                <textarea
                                    value={bulkInput}
                                    onChange={(e) => setBulkInput(e.target.value)}
                                    placeholder="Ejemplo: 101, 102, 103, 205..."
                                    rows={3}
                                    className="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 font-mono text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#006BB8] focus:ring-2 focus:ring-blue-100 focus:outline-none"
                                />
                                <div className="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setShowBulkMode(false)}
                                        className="rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        type="button"
                                        onClick={handleApplyBulk}
                                        className="inline-flex items-center gap-1.5 rounded-lg bg-[#004B87] px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-[#003865]"
                                    >
                                        Aplicar a lista
                                    </button>
                                </div>
                            </div>
                        ) : null}

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                {rows.map((row, index) => (
                                    <div
                                        key={row.id}
                                        className="group relative flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1.5 transition focus-within:border-[#004B87] focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100"
                                    >
                                        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#003865] text-xs font-black text-white">
                                            #{index + 1}
                                        </div>

                                        <input
                                            type="number"
                                            min="1"
                                            step="1"
                                            value={row.value}
                                            onChange={(event) => handleChange(row.id, event.target.value)}
                                            placeholder="N° interno de tarjeta..."
                                            className="w-full bg-transparent px-3 py-2 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none"
                                            aria-label={`Tarjeta ${index + 1}`}
                                        />

                                        <button
                                            type="button"
                                            onClick={() => removeRow(row.id)}
                                            title="Eliminar este campo"
                                            className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-[#E30613]"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </div>
                                ))}
                            </div>

                            <div className="flex flex-wrap items-center justify-between gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={addRow}
                                    className="inline-flex items-center gap-2 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-2.5 text-xs font-bold text-[#004B87] transition hover:border-[#004B87] hover:bg-blue-50"
                                >
                                    <Plus className="h-4 w-4 text-[#E30613]" />
                                    Agregar otra tarjeta
                                </button>

                                <div className="flex items-center gap-3">
                                    <span className="text-xs font-semibold text-slate-500">
                                        {rows.filter((r) => r.value.trim()).length} tarjeta(s) en lista
                                    </span>
                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#004B87] to-[#006BB8] px-6 py-2.5 text-sm font-black text-white shadow-md shadow-blue-900/20 transition hover:from-[#003865] hover:to-[#005799] focus:ring-2 focus:ring-[#004B87] focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {loading ? (
                                            <>
                                                <Loader2 className="h-4 w-4 animate-spin" />
                                                <span>Consultando Metro Cali...</span>
                                            </>
                                        ) : (
                                            <>
                                                <Search className="h-4 w-4" />
                                                <span>Consultar Tarjetas MIO</span>
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {/* Mensaje de Error si ocurre */}
                    {error && (
                        <div className="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-900">
                            <AlertCircle className="mt-0.5 h-5 w-5 shrink-0 text-[#E30613]" />
                            <div className="flex-1 text-sm">
                                <p className="font-bold text-[#E30613]">Error en la consulta de Metro Cali:</p>
                                <p className="mt-0.5 text-slate-700">{error}</p>
                            </div>
                        </div>
                    )}

                    {/* Barra de Estadísticas y Resultados */}
                    {mappedResults.length > 0 && (
                        <div className="space-y-6">
                            {/* Panel de Métricas Rápidas en Fondo Claro */}
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                                    <p className="text-xs font-bold tracking-wider text-slate-500 uppercase">Total Consultadas</p>
                                    <p className="mt-1 text-2xl font-black text-[#002D54]">{mappedResults.length}</p>
                                </div>
                                <div className="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-xs">
                                    <p className="flex items-center gap-1.5 text-xs font-bold tracking-wider text-emerald-800 uppercase">
                                        <CheckCircle2 className="h-3.5 w-3.5 text-emerald-600" />
                                        Encontradas
                                    </p>
                                    <p className="mt-1 text-2xl font-black text-emerald-700">{totalFound}</p>
                                </div>
                                <div className="rounded-2xl border border-red-200 bg-red-50/70 p-4 shadow-xs">
                                    <p className="flex items-center gap-1.5 text-xs font-bold tracking-wider text-red-800 uppercase">
                                        <XCircle className="h-3.5 w-3.5 text-[#E30613]" />
                                        No Encontradas
                                    </p>
                                    <p className="mt-1 text-2xl font-black text-[#E30613]">{totalNotFound}</p>
                                </div>
                                <div className="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 shadow-xs">
                                    <p className="text-xs font-bold tracking-wider text-[#004B87] uppercase">Tiempo de Respuesta</p>
                                    <p className="mt-1 text-2xl font-black text-[#004B87]">
                                        {executionTimeMs !== null ? `${executionTimeMs} ms` : '—'}
                                    </p>
                                </div>
                            </div>

                            {/* Contenedor Principal de la Tabla / JSON en Blanco */}
                            <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                {/* Barra Superior de Acciones de la Tabla */}
                                <div className="flex flex-col gap-4 border-b border-slate-200 bg-slate-50/80 p-4 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
                                    {/* Pestañas de Vista */}
                                    <div className="flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-2xs">
                                        <button
                                            type="button"
                                            onClick={() => setActiveTab('table')}
                                            className={`inline-flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs font-bold transition ${
                                                activeTab === 'table'
                                                    ? 'bg-[#003865] text-white shadow-xs'
                                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                            }`}
                                        >
                                            <TableIcon className="h-3.5 w-3.5" />
                                            <span>Tabla Comprensible</span>
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setActiveTab('cards')}
                                            className={`inline-flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs font-bold transition ${
                                                activeTab === 'cards'
                                                    ? 'bg-[#003865] text-white shadow-xs'
                                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                            }`}
                                        >
                                            <LayoutGrid className="h-3.5 w-3.5" />
                                            <span>Fichas de Tarjetas</span>
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setActiveTab('json')}
                                            className={`inline-flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs font-bold transition ${
                                                activeTab === 'json'
                                                    ? 'bg-[#003865] text-white shadow-xs'
                                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                            }`}
                                        >
                                            <Code2 className="h-3.5 w-3.5" />
                                            <span>JSON Crudo</span>
                                        </button>
                                    </div>

                                    {/* Botones Principales con Rojo MIO y Azul Metrocali */}
                                    <div className="flex flex-wrap items-center gap-2.5">
                                        {/* BOTÓN PROMINENTE COPIAR DATOS EN ROJO MIO EMBLEMÁTICO */}
                                        <button
                                            type="button"
                                            onClick={() => handleCopyJson(apiResponse ?? mappedResults)}
                                            className={`inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-black shadow-md transition ${
                                                copiedGlobal
                                                    ? 'bg-emerald-600 text-white shadow-emerald-600/30'
                                                    : 'bg-gradient-to-r from-[#E30613] to-[#C40510] text-white shadow-red-600/25 hover:from-[#C40510] hover:to-[#A3040D]'
                                            }`}
                                            title="Copiar el JSON completo de respuesta al portapapeles"
                                        >
                                            {copiedGlobal ? (
                                                <>
                                                    <Check className="h-4 w-4" />
                                                    <span>¡Datos JSON Copiados!</span>
                                                </>
                                            ) : (
                                                <>
                                                    <Copy className="h-4 w-4" />
                                                    <span>Copiar datos (JSON)</span>
                                                </>
                                            )}
                                        </button>

                                        <button
                                            type="button"
                                            onClick={handleDownloadJson}
                                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-[#003865] shadow-2xs transition hover:border-[#004B87] hover:bg-blue-50"
                                            title="Descargar archivo JSON con los datos de Metro Cali"
                                        >
                                            <Download className="h-3.5 w-3.5 text-[#004B87]" />
                                            <span>Descargar JSON</span>
                                        </button>
                                    </div>
                                </div>

                                {/* Barra de Filtros y Búsqueda */}
                                <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:px-5">
                                    <div className="flex items-center gap-2">
                                        <span className="text-xs font-bold text-slate-500">Filtrar estado:</span>
                                        <div className="flex rounded-lg border border-slate-200 bg-slate-50 p-0.5 text-xs">
                                            <button
                                                type="button"
                                                onClick={() => setStatusFilter('all')}
                                                className={`rounded-md px-2.5 py-1 font-bold transition ${
                                                    statusFilter === 'all'
                                                        ? 'bg-[#003865] text-white shadow-xs'
                                                        : 'text-slate-600 hover:text-slate-900'
                                                }`}
                                            >
                                                Todas ({mappedResults.length})
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => setStatusFilter('found')}
                                                className={`rounded-md px-2.5 py-1 font-bold transition ${
                                                    statusFilter === 'found'
                                                        ? 'bg-emerald-600 text-white shadow-xs'
                                                        : 'text-slate-600 hover:text-emerald-700'
                                                }`}
                                            >
                                                Encontradas ({totalFound})
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => setStatusFilter('not_found')}
                                                className={`rounded-md px-2.5 py-1 font-bold transition ${
                                                    statusFilter === 'not_found'
                                                        ? 'bg-[#E30613] text-white shadow-xs'
                                                        : 'text-slate-600 hover:text-[#E30613]'
                                                }`}
                                            >
                                                No encontradas ({totalNotFound})
                                            </button>
                                        </div>
                                    </div>

                                    {/* Input de Búsqueda Rápida */}
                                    <div className="relative w-full sm:w-64">
                                        <Search className="absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                                        <input
                                            type="text"
                                            value={filterQuery}
                                            onChange={(e) => setFilterQuery(e.target.value)}
                                            placeholder="Buscar tarjeta, serie, estado..."
                                            className="w-full rounded-xl border border-slate-300 bg-white py-1.5 pr-3 pl-9 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#004B87] focus:ring-2 focus:ring-blue-100 focus:outline-none"
                                        />
                                    </div>
                                </div>

                                {/* CONTENIDO SEGÚN LA PESTAÑA SELECCIONADA */}

                                {/* 1. VISTA TABLA COMPRENSIBLE (Fondo Blanco Claro y Cabecera Azul Suave) */}
                                {activeTab === 'table' && (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-slate-200 text-left text-xs">
                                            <thead className="bg-[#EBF3FC] font-black tracking-wider text-[#003865] uppercase">
                                                <tr>
                                                    <th className="w-12 px-4 py-3 text-center">#</th>
                                                    <th className="px-4 py-3">Estado</th>
                                                    <th className="px-4 py-3">Input Consultado</th>
                                                    <th className="px-4 py-3">N° Interno (crd_intsnr)</th>
                                                    <th className="px-4 py-3">N° Serie (crd_snr)</th>
                                                    <th className="px-4 py-3">Tipo Tarjeta</th>
                                                    <th className="px-4 py-3">Estado Operativo</th>
                                                    <th className="px-4 py-3">Personalizada</th>
                                                    <th className="px-4 py-3 text-right">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-100 bg-white">
                                                {filteredResults.length === 0 ? (
                                                    <tr>
                                                        <td colSpan={9} className="px-4 py-8 text-center text-slate-500">
                                                            No hay registros que coincidan con los filtros aplicados.
                                                        </td>
                                                    </tr>
                                                ) : (
                                                    filteredResults.map((item, index) => {
                                                        const isNotFound = Boolean(item.result?.not_found);
                                                        const isExpanded = expandedCardIndex === index;

                                                        return (
                                                            <React.Fragment key={`${item.input}-${index}`}>
                                                                <tr className={`transition hover:bg-[#F0F7FF] ${isNotFound ? 'bg-red-50/40' : ''}`}>
                                                                    <td className="px-4 py-3 text-center font-mono font-bold text-slate-400">
                                                                        {index + 1}
                                                                    </td>
                                                                    <td className="px-4 py-3 whitespace-nowrap">
                                                                        {isNotFound ? (
                                                                            <span className="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-2.5 py-0.5 text-xs font-bold text-[#E30613]">
                                                                                <XCircle className="h-3 w-3 text-[#E30613]" />
                                                                                No encontrada
                                                                            </span>
                                                                        ) : (
                                                                            <span className="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                                                                <CheckCircle2 className="h-3 w-3 text-emerald-600" />
                                                                                Encontrada
                                                                            </span>
                                                                        )}
                                                                    </td>
                                                                    <td className="px-4 py-3 font-mono font-black text-[#002D54]">
                                                                        {String(item.input)}
                                                                    </td>
                                                                    <td className="px-4 py-3 font-mono font-semibold text-slate-700">
                                                                        {item.result?.crd_intsnr !== undefined ? String(item.result.crd_intsnr) : '—'}
                                                                    </td>
                                                                    <td className="px-4 py-3 font-mono text-slate-700">
                                                                        {item.result?.crd_snr !== undefined ? String(item.result.crd_snr) : '—'}
                                                                    </td>
                                                                    <td className="px-4 py-3 font-mono text-slate-700">
                                                                        {item.result?.cty_id !== undefined
                                                                            ? renderFormattedValue('cty_id', item.result.cty_id)
                                                                            : '—'}
                                                                    </td>
                                                                    <td className="px-4 py-3">
                                                                        {item.result?.crd_status !== undefined ? (
                                                                            renderFormattedValue('crd_status', item.result.crd_status)
                                                                        ) : isNotFound ? (
                                                                            <span className="text-xs font-semibold text-[#E30613] italic">
                                                                                Inexistente en Metro Cali
                                                                            </span>
                                                                        ) : (
                                                                            '—'
                                                                        )}
                                                                    </td>
                                                                    <td className="px-4 py-3">
                                                                        {item.result?.is_personalized !== undefined
                                                                            ? renderFormattedValue('is_personalized', item.result.is_personalized)
                                                                            : '—'}
                                                                    </td>
                                                                    <td className="space-x-1.5 px-4 py-3 text-right whitespace-nowrap">
                                                                        <button
                                                                            type="button"
                                                                            onClick={() => setExpandedCardIndex(isExpanded ? null : index)}
                                                                            className={`inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold transition ${
                                                                                isExpanded
                                                                                    ? 'bg-[#003865] text-white shadow-2xs'
                                                                                    : 'border border-slate-200 bg-slate-100 text-[#003865] hover:bg-[#EBF3FC]'
                                                                            }`}
                                                                        >
                                                                            <Eye className="h-3 w-3 text-[#004B87]" />
                                                                            {isExpanded ? 'Ocultar' : 'Detalles'}
                                                                            {isExpanded ? (
                                                                                <ChevronUp className="h-3 w-3" />
                                                                            ) : (
                                                                                <ChevronDown className="h-3 w-3" />
                                                                            )}
                                                                        </button>
                                                                        <button
                                                                            type="button"
                                                                            onClick={() => handleCopyJson(item.result, `${item.input}-${index}`)}
                                                                            className="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 shadow-2xs transition hover:border-[#004B87] hover:bg-blue-50"
                                                                            title="Copiar JSON de esta tarjeta"
                                                                        >
                                                                            {copiedRowId === `${item.input}-${index}` ? (
                                                                                <Check className="h-3 w-3 font-bold text-emerald-600" />
                                                                            ) : (
                                                                                <Copy className="h-3 w-3 text-[#E30613]" />
                                                                            )}
                                                                        </button>
                                                                    </td>
                                                                </tr>

                                                                {/* Fila expandible con tabla de desglose completa */}
                                                                {isExpanded && (
                                                                    <tr className="bg-[#F7FAFD]">
                                                                        <td colSpan={9} className="p-4 sm:p-6">
                                                                            <div className="rounded-xl border border-blue-200 bg-white p-4 shadow-sm sm:p-5">
                                                                                <div className="mb-3 flex items-center justify-between border-b border-slate-100 pb-3">
                                                                                    <div>
                                                                                        <h4 className="flex items-center gap-2 text-sm font-black text-[#002D54]">
                                                                                            <span>Propiedades de Tarjeta MIO: {item.input}</span>
                                                                                            {isNotFound && (
                                                                                                <span className="rounded border border-red-200 bg-red-100 px-2 py-0.5 text-[10px] font-bold text-[#E30613]">
                                                                                                    NO REGISTRADA EN METRO CALI
                                                                                                </span>
                                                                                            )}
                                                                                        </h4>
                                                                                        <p className="text-xs text-slate-500">
                                                                                            Desglose detallado de todos los atributos devueltos por el
                                                                                            sistema.
                                                                                        </p>
                                                                                    </div>
                                                                                    <button
                                                                                        type="button"
                                                                                        onClick={() => handleCopyJson(item.result, `detail-${index}`)}
                                                                                        className="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-[#003865] transition hover:bg-blue-50"
                                                                                    >
                                                                                        {copiedRowId === `detail-${index}` ? (
                                                                                            <>
                                                                                                <Check className="h-3 w-3 text-emerald-600" />
                                                                                                <span>¡Copiado!</span>
                                                                                            </>
                                                                                        ) : (
                                                                                            <>
                                                                                                <Copy className="h-3 w-3 text-[#E30613]" />
                                                                                                <span>Copiar JSON individual</span>
                                                                                            </>
                                                                                        )}
                                                                                    </button>
                                                                                </div>

                                                                                {/* Tabla de Par Clave-Valor Comprensible */}
                                                                                <div className="overflow-x-auto rounded-lg border border-slate-200">
                                                                                    <table className="min-w-full divide-y divide-slate-200 text-xs">
                                                                                        <thead className="bg-[#EBF3FC] text-[#003865]">
                                                                                            <tr>
                                                                                                <th className="px-3.5 py-2 font-bold">
                                                                                                    Campo / Atributo MIO
                                                                                                </th>
                                                                                                <th className="px-3.5 py-2 font-bold">
                                                                                                    Clave Técnica
                                                                                                </th>
                                                                                                <th className="px-3.5 py-2 font-bold">Valor</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody className="divide-y divide-slate-100 bg-white">
                                                                                            {Object.entries(item.result).map(([k, val]) => (
                                                                                                <tr key={k} className="hover:bg-slate-50">
                                                                                                    <td className="px-3.5 py-2 font-bold text-[#002D54]">
                                                                                                        {getFieldLabel(k)}
                                                                                                    </td>
                                                                                                    <td className="px-3.5 py-2 font-mono text-slate-500">
                                                                                                        <span className="rounded border border-slate-200 bg-slate-100 px-1.5 py-0.5 text-[11px]">
                                                                                                            {k}
                                                                                                        </span>
                                                                                                    </td>
                                                                                                    <td className="px-3.5 py-2">
                                                                                                        {renderFormattedValue(k, val)}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            ))}
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                )}
                                                            </React.Fragment>
                                                        );
                                                    })
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {/* 2. VISTA EN FICHAS / TARJETERAS */}
                                {activeTab === 'cards' && (
                                    <div className="grid grid-cols-1 gap-4 bg-[#F8FAFC] p-5 md:grid-cols-2 lg:grid-cols-3">
                                        {filteredResults.map((item, index) => {
                                            const isNotFound = Boolean(item.result?.not_found);

                                            return (
                                                <div
                                                    key={`card-view-${item.input}-${index}`}
                                                    className={`rounded-xl border p-4 shadow-xs transition ${
                                                        isNotFound
                                                            ? 'border-red-200 bg-red-50/50'
                                                            : 'border-slate-200 bg-white hover:border-[#004B87] hover:shadow-sm'
                                                    }`}
                                                >
                                                    <div className="mb-3 flex items-center justify-between border-b border-slate-100 pb-3">
                                                        <div>
                                                            <span className="text-[11px] font-black tracking-wider text-[#004B87] uppercase">
                                                                Tarjeta MIO #{index + 1}
                                                            </span>
                                                            <h4 className="font-mono text-base font-black text-[#002D54]">Input: {item.input}</h4>
                                                        </div>
                                                        <div>
                                                            {isNotFound ? (
                                                                <span className="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-100 px-2.5 py-1 text-xs font-bold text-[#E30613]">
                                                                    No encontrada
                                                                </span>
                                                            ) : (
                                                                <span className="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">
                                                                    Encontrada
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>

                                                    <div className="space-y-2 text-xs">
                                                        {isNotFound ? (
                                                            <div className="rounded-lg border border-red-200 bg-white p-3 text-xs text-red-900 shadow-2xs">
                                                                {String(
                                                                    item.result.message ??
                                                                        'No se encontró registro para este identificador en Metro Cali.',
                                                                )}
                                                            </div>
                                                        ) : (
                                                            Object.entries(item.result)
                                                                .filter(([k]) => !['not_found', 'message'].includes(k))
                                                                .slice(0, 8)
                                                                .map(([k, val]) => (
                                                                    <div
                                                                        key={k}
                                                                        className="flex items-center justify-between gap-2 border-b border-slate-100 py-1"
                                                                    >
                                                                        <span className="max-w-[140px] truncate font-bold text-slate-600">
                                                                            {getFieldLabel(k)}:
                                                                        </span>
                                                                        <div className="truncate text-right">{renderFormattedValue(k, val)}</div>
                                                                    </div>
                                                                ))
                                                        )}
                                                    </div>

                                                    <div className="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                                        <button
                                                            type="button"
                                                            onClick={() => {
                                                                setActiveTab('table');
                                                                setExpandedCardIndex(index);
                                                            }}
                                                            className="text-xs font-black text-[#004B87] hover:text-[#006BB8]"
                                                        >
                                                            Ver todos los campos →
                                                        </button>

                                                        <button
                                                            type="button"
                                                            onClick={() => handleCopyJson(item.result, `card-grid-${index}`)}
                                                            className="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-bold text-[#003865] hover:bg-blue-50"
                                                        >
                                                            {copiedRowId === `card-grid-${index}` ? (
                                                                <>
                                                                    <Check className="h-3 w-3 text-emerald-600" />
                                                                    <span>Copiado</span>
                                                                </>
                                                            ) : (
                                                                <>
                                                                    <Copy className="h-3 w-3 text-[#E30613]" />
                                                                    <span>JSON</span>
                                                                </>
                                                            )}
                                                        </button>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}

                                {/* 3. VISTA JSON CRUDO (Editor Encapsulado en Contraste Legible) */}
                                {activeTab === 'json' && (
                                    <div className="bg-slate-900 p-4 text-white sm:p-6">
                                        <div className="mb-3 flex items-center justify-between">
                                            <div className="flex items-center gap-2">
                                                <FileJson className="h-4 w-4 text-[#E30613]" />
                                                <span className="text-xs font-black tracking-wider text-slate-200 uppercase">
                                                    Respuesta JSON Estructurada • Servidor Metro Cali
                                                </span>
                                            </div>

                                            <button
                                                type="button"
                                                onClick={() => handleCopyJson(apiResponse ?? mappedResults)}
                                                className={`inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold transition ${
                                                    copiedGlobal
                                                        ? 'bg-emerald-600 font-black text-white'
                                                        : 'bg-gradient-to-r from-[#E30613] to-[#C40510] text-white hover:from-[#C40510] hover:to-[#A3040D]'
                                                }`}
                                            >
                                                {copiedGlobal ? (
                                                    <>
                                                        <Check className="h-3.5 w-3.5" />
                                                        <span>¡JSON Copiado al portapapeles!</span>
                                                    </>
                                                ) : (
                                                    <>
                                                        <Copy className="h-3.5 w-3.5" />
                                                        <span>Copiar JSON completo</span>
                                                    </>
                                                )}
                                            </button>
                                        </div>

                                        <pre className="max-h-[600px] overflow-auto rounded-xl border border-slate-700 bg-slate-950 p-4 font-mono text-xs leading-relaxed text-cyan-300 shadow-inner">
                                            {JSON.stringify(apiResponse ?? mappedResults, null, 2)}
                                        </pre>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {/* Estado cuando la API respondió pero no hubo mapeo */}
                    {apiResponse && mappedResults.length === 0 && (
                        <div className="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div className="mb-3 flex items-center justify-between">
                                <h3 className="text-lg font-black text-[#002D54]">Respuesta del Servidor de Metro Cali</h3>
                                <button
                                    type="button"
                                    onClick={() => handleCopyJson(apiResponse)}
                                    className="inline-flex items-center gap-1.5 rounded-lg bg-[#E30613] px-3.5 py-1.5 text-xs font-bold text-white hover:bg-[#C40510]"
                                >
                                    <Copy className="h-3.5 w-3.5" />
                                    <span>Copiar datos</span>
                                </button>
                            </div>
                            <pre className="overflow-x-auto rounded-xl border border-slate-200 bg-slate-50 p-4 font-mono text-xs text-slate-800">
                                {JSON.stringify(apiResponse, null, 2)}
                            </pre>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
