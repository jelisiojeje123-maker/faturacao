-- =============================================================
-- SEED DATA - Sistema de Faturação - Moçambique
-- Execute APÓS o schema.sql
-- =============================================================

USE `faturacao_mz`;

-- Utilizadores (password = "12345678" hasheada com bcrypt)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Administrador', 'admin@empresa.mz', '$2y$12$WIsMZlb2rthqTw4QSfNa9OQsymhpP5Og712m2vNOic8y8QOuB3oo6', 'admin'),
('Operador Teste', 'operador@empresa.mz', '$2y$12$WIsMZlb2rthqTw4QSfNa9OQsymhpP5Og712m2vNOic8y8QOuB3oo6', 'operador');

-- Configuração da empresa
INSERT INTO `company_settings` (`name`, `nuit`, `email`, `phone`, `address`, `city`, `iva_rate`, `currency`, `invoice_prefix`, `next_invoice_number`, `payment_terms`, `bank_name`, `bank_account`) VALUES
('Minha Empresa, Lda.', '400 000 001', 'geral@minhaempresa.mz', '+258 21 123 456', 'Av. Julius Nyerere, 123', 'Maputo', 16.00, 'MT', 'FAT', 1,
'Pagamento a 30 dias após emissão da fatura. Métodos aceites: Transferência bancária, M-Pesa, e-Mola.',
'Millennium BIM', '123456789');

-- Clientes
INSERT INTO `clients` (`name`, `nuit`, `email`, `phone`, `address`, `city`, `status`) VALUES
('João Delgado', '400 123 456', 'joao.delgado@email.com', '+258 84 123 4567', 'Rua da Paz, 10, Maputo', 'Maputo', 'ativo'),
('Maria Langa', '102 345 678', 'maria.langa@corporativo.mz', '+258 82 987 6543', 'Av. Eduardo Mondlane, 45', 'Maputo', 'ativo'),
('Sitoe Transportes, Lda.', '500 789 012', 'financeiro@sitoetrans.co.mz', '+258 21 445 667', 'Zona Industrial, Matola', 'Matola', 'ativo'),
('Fernando Chicua', '301 222 333', 'fernando@techsolutions.mz', '+258 85 555 1234', 'Bairro Central, Beira', 'Beira', 'inativo'),
('Construtora Nkomati, SA', '600 111 222', 'financeiro@nkomati.mz', '+258 21 789 000', 'Av. Marginal, 200', 'Maputo', 'ativo'),
('Ana Muchanga', '201 333 444', 'ana.m@gmail.com', '+258 86 234 5678', 'Alto Maé, Maputo', 'Maputo', 'ativo');

-- Serviços
INSERT INTO `services` (`name`, `description`, `price`, `unit`, `type`, `iva_exempt`) VALUES
('Consultoria de TI', 'Consultoria e assessoria em tecnologia de informação', 5000.00, 'hora', 'pontual', 0),
('Desenvolvimento Web', 'Criação e desenvolvimento de websites e aplicações web', 35000.00, 'projecto', 'pontual', 0),
('Manutenção Mensal', 'Serviço de manutenção e suporte técnico mensal', 8000.00, 'mês', 'recorrente', 0),
('Design Gráfico', 'Criação de materiais gráficos e identidade visual', 3500.00, 'projecto', 'pontual', 0),
('Formação / Treino', 'Sessões de formação e capacitação de equipas', 2500.00, 'sessão', 'pontual', 0),
('Auditoria de Sistemas', 'Auditoria e análise de segurança de sistemas', 12000.00, 'projecto', 'pontual', 0),
('Hospedagem Web (Anual)', 'Alojamento de website - plano anual', 4800.00, 'ano', 'recorrente', 0),
('SEO e Marketing Digital', 'Optimização para motores de busca e campanhas digitais', 6000.00, 'mês', 'recorrente', 0);

-- Faturas de exemplo
INSERT INTO `invoices` (`client_id`, `invoice_number`, `issue_date`, `due_date`, `status`, `subtotal`, `iva_rate`, `iva_amount`, `total`, `amount_paid`, `amount_due`, `notes`, `created_by`) VALUES
(1, 'FAT-2025-001', '2025-03-01', '2025-03-31', 'paga',    43103.45, 16.00, 6896.55, 50000.00, 50000.00, 0.00,     'Projecto concluído com sucesso.', 1),
(2, 'FAT-2025-002', '2025-03-15', '2025-04-14', 'emitida', 37931.03, 16.00, 6068.97, 44000.00, 0.00,     44000.00, NULL, 1),
(3, 'FAT-2025-003', '2025-02-01', '2025-03-02', 'vencida', 17241.38, 16.00, 2758.62, 20000.00, 0.00,     20000.00, 'Serviço de manutenção - Fevereiro.', 1),
(5, 'FAT-2025-004', '2025-04-01', '2025-04-30', 'emitida', 10344.83, 16.00, 1655.17, 12000.00, 5000.00,  7000.00,  NULL, 1),
(1, 'FAT-2025-005', '2025-04-10', '2025-05-10', 'rascunho',3017.24,  16.00, 482.76,  3500.00,  0.00,     3500.00,  NULL, 1);

-- Itens das faturas
INSERT INTO `invoice_items` (`invoice_id`, `service_id`, `description`, `quantity`, `unit_price`, `total`, `sort_order`) VALUES
(1, 2, 'Desenvolvimento Web - Portal Corporativo', 1, 35000.00, 35000.00, 1),
(1, 1, 'Consultoria de TI', 2, 5000.00, 10000.00, 2),
(2, 3, 'Manutenção Mensal - Março 2025', 1, 8000.00, 8000.00, 1),
(2, 5, 'Formação de Equipas - Excel Avançado', 3, 2500.00, 7500.00, 2),
(3, 3, 'Manutenção Mensal - Fevereiro 2025', 1, 8000.00, 8000.00, 1),
(4, 1, 'Auditoria de Sistemas', 1, 12000.00, 12000.00, 1),
(5, 4, 'Design Gráfico - Brochura Institucional', 1, 3500.00, 3500.00, 1);

-- Pagamentos
INSERT INTO `payments` (`invoice_id`, `amount`, `payment_date`, `method`, `reference`, `receipt_number`, `created_by`) VALUES
(1, 50000.00, '2025-03-28', 'transferencia', 'TRF-BIM-20250328-001', 'REC-2025-001', 1),
(4, 5000.00,  '2025-04-15', 'mobile_money',  'MPESA-20250415-XYZ',  'REC-2025-002', 1);
