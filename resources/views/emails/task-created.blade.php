<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Tarefa Criada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .task-details {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #4F46E5;
        }
        .label {
            font-weight: bold;
            color: #4F46E5;
        }
        .priority {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .priority-low { background-color: #dbeafe; color: #1e40af; }
        .priority-medium { background-color: #fef3c7; color: #92400e; }
        .priority-high { background-color: #fee2e2; color: #991b1b; }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nova Tarefa Criada</h1>
    </div>

    <div class="content">
        <p>Olá, <strong>{{ $task->user->name }}</strong>!</p>

        <p>Uma nova tarefa foi criada com sucesso no projeto <strong>{{ $task->project->name }}</strong>.</p>

        <div class="task-details">
            <p><span class="label">Título:</span> {{ $task->title }}</p>

            @if($task->description)
                <p><span class="label">Descrição:</span><br>{{ $task->description }}</p>
            @endif

            <p>
                <span class="label">Prioridade:</span>
                <span class="priority priority-{{ strtolower($task->priority->name) }}">
                    {{ $task->priority->label() }}
                </span>
            </p>

            <p><span class="label">Estado:</span> {{ $task->status->label() }}</p>

            @if($task->deadline)
                <p><span class="label">Prazo:</span> {{ $task->deadline->format('d/m/Y') }}</p>
            @endif

            <p><span class="label">Criada em:</span> {{ $task->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <p>Você pode visualizar e gerenciar esta tarefa acessando o sistema TaskFlow.</p>
    </div>

    <div class="footer">
        <p>Este é um email automático. Por favor, não responda.</p>
        <p>&copy; {{ date('Y') }} TaskFlow. Todos os direitos reservados.</p>
    </div>
</body>
</html>
