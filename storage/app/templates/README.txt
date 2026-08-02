TEMPLATE .DOCX FILES
====================

Place template .docx files in this directory for Word export.

File naming convention:
  - default.docx           : Default template for all tenants
  - tenant_{id}.docx       : Tenant-specific template (e.g. tenant_1.docx)

Supported Placeholders:
  {nama_universitas}       : University name
  {nama_fakultas}          : Faculty name
  {nama_prodi}             : Study program name
  {jenjang}                : Program level (D3/S1/S2)
  {kode_mk}                : Course code
  {nama_mk}                : Course name
  {sks}                    : Credit hours
  {semester}               : Semester number
  {tahun_akademik}         : Academic year
  {nama_semester}          : Semester name
  {deskripsi_mk}           : Course description
  {dosen_pengampu}         : Lecturer name(s)
  {kaprodi}                : Head of study program
  {dekan}                  : Dean name
  {tanggal}                : Export date
  {akreditasi_prodi}       : Study program accreditation

Placeholders use PHPWord TemplateProcessor with ${placeholder} or {placeholder} syntax.
