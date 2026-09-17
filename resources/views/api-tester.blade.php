<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --border: #dbe3ef;
            --success: #166534;
            --success-bg: #ecfdf5;
            --warning: #92400e;
            --warning-bg: #fffbeb;
            --error: #991b1b;
            --error-bg: #fef2f2;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
            padding: 28px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        p.subtitle {
            margin-top: 0;
            color: var(--muted);
            margin-bottom: 24px;
        }

        .card-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 12px;
        }

        input[type="number"] {
            flex: 1;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 1rem;
        }

        button {
            border: 0;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-add {
            background: #e0f2fe;
            color: #0c4a6e;
            margin-top: 8px;
        }

        .btn-remove {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert {
            margin-top: 20px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
        }

        .alert.error {
            background: var(--error-bg);
            color: var(--error);
            border-color: #fecaca;
        }

        .result-box {
            margin-top: 24px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 16px;
        }

        .result-box h2 {
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 1.15rem;
        }

        .input-result {
            border: 1px solid var(--border);
            background: white;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }

        .input-result-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: var(--success-bg);
            color: var(--success);
        }

        .badge-warning {
            background: var(--warning-bg);
            color: var(--warning);
        }

        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: Consolas, Monaco, monospace;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .small {
            color: var(--muted);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>Probador de API</h1>
            <p class="subtitle">Ingresa una o varias tarjetas y compara cada entrada con su respuesta.</p>

            <form method="POST" action="{{ route('api.tester.submit') }}">
                @csrf

                <div id="cards-container">
                    @php
                    $savedCards = is_array($cards ?? []) ? $cards : [];
                    @endphp

                    @if(empty($savedCards))
                    <div class="card-row">
                        <input type="number" name="cards[]" value="" placeholder="Número interno de tarjeta" min="1" step="1" required>
                        <button type="button" class="btn-remove remove-input">Eliminar</button>
                    </div>
                    @else
                    @foreach($savedCards as $index => $value)
                    <div class="card-row">
                        <input type="number" name="cards[]" value="{{ $value }}" placeholder="Número interno de tarjeta" min="1" step="1" required>
                        <button type="button" class="btn-remove remove-input">Eliminar</button>
                    </div>
                    @endforeach
                    @endif
                </div>

                <button type="button" id="add-card" class="btn-add">+ Agregar input</button>

                <div style="margin-top: 18px;">
                    <button type="submit" class="btn-primary">Consultar API</button>
                </div>
            </form>

            @if($error)
            <div class="alert error">{{ $error }}</div>
            @endif

            @if(!empty($mapped_results))
            <div class="result-box">
                <h2>Relación input → resultado</h2>

                @foreach($mapped_results as $item)
                <div class="input-result">
                    <div class="input-result-header">
                        <span>Input: {{ $item['input'] }}</span>
                        @if(!empty($item['result']['not_found'] ?? false))
                        <span class="badge badge-warning">Sin resultado</span>
                        @else
                        <span class="badge badge-success">Con resultado</span>
                        @endif
                    </div>

                    @if(!empty($item['result']['not_found'] ?? false))
                    <div class="small">{{ $item['result']['message'] ?? 'No se encontró ninguna tarjeta para este valor.' }}</div>
                    @else
                    <pre>{{ json_encode($item['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif
                </div>
                @endforeach
            </div>
            @elseif($result !== null)
            <div class="result-box">
                <h2>Respuesta completa</h2>
                <pre>{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
    </div>

    <script>
        const container = document.getElementById('cards-container');
        const addButton = document.getElementById('add-card');

        function createInputRow(value = '') {
            const row = document.createElement('div');
            row.className = 'card-row';
            row.innerHTML = `
                <input type="number" name="cards[]" value="${value}" placeholder="Número interno de tarjeta" min="1" step="1" required>
                <button type="button" class="btn-remove remove-input">Eliminar</button>
            `;

            row.querySelector('.remove-input').addEventListener('click', function() {
                const rows = container.querySelectorAll('.card-row');

                if (rows.length > 1) {
                    row.remove();
                } else {
                    row.querySelector('input').value = '';
                }
            });

            return row;
        }

        addButton.addEventListener('click', function() {
            container.appendChild(createInputRow(''));
        });

        document.querySelectorAll('.remove-input').forEach((button) => {
            button.addEventListener('click', function() {
                const row = button.closest('.card-row');
                const rows = container.querySelectorAll('.card-row');

                if (rows.length > 1) {
                    row.remove();
                } else {
                    row.querySelector('input').value = '';
                }
            });
        });
    </script>
</body>

</html>