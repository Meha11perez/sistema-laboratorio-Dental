
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
            background: #f1f5f9;
            color: #1e293b;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .rotulo {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #94a3b8;
            border-radius: 10px;
        }

        .encabezado {
            padding-bottom: 16px;
            margin-bottom: 18px;
            border-bottom: 2px solid #7ba7b5;
            text-align: center;
        }

        .logo {
            display: block;
            width: 230px;
            max-width: 100%;
            height: auto;
            margin: 0 auto 12px;
        }

        .encabezado h1 {
            margin: 0;
            color: #355c78;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .codigo-orden {
            margin: 16px 0;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-align: center;
        }

        .codigo-orden span {
            display: block;
            color: #475569;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .codigo-orden strong {
            display: block;
            margin-top: 3px;
            font-size: 19px;
            overflow-wrap: anywhere;
        }

        .datos p {
            margin: 0;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            overflow-wrap: anywhere;
        }

        .datos strong {
            color: #334155;
        }

        .especificaciones {
            margin-top: 16px;
        }

        .especificaciones strong {
            display: block;
            margin-bottom: 6px;
            color: #334155;
        }

        .especificaciones p {
            margin: 0;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .fecha {
            margin: 16px 0 0;
            color: #475569;
            font-size: 12px;
            text-align: right;
        }

        .acciones {
            width: 100%;
            max-width: 440px;
            margin: 16px auto 0;
        }

        .boton-imprimir {
            display: block;
            width: 100%;
            padding: 12px 16px;
            border: 0;
            border-radius: 8px;
            background: #355c78;
            color: #ffffff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .boton-imprimir:hover {
            background: #29485e;
        }

        .boton-imprimir:focus-visible {
            outline: 3px solid #7ba7b5;
            outline-offset: 3px;
        }

        @page {
            margin: 10mm;
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
                color: #000000;
            }

            .rotulo {
                max-width: 110mm;
                padding: 6mm;
                border: 1px solid #64748b;
                border-radius: 0;
            }

            .acciones {
                display: none;
            }
        }
    </style>