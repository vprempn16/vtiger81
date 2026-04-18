# ServiceCompetency Module - Consultant Query Changes

## Overview
This document describes the changes made to the consultant availability calculation in the `getCustomQueryForPopup` function to ensure accurate consultant availability calculations.

## Changes Made

### 1. Fixed Ticket Counting Logic
**Problem**: Original query counted total tickets instead of unique days with tickets.
**Solution**: Changed from `COUNT(*)` to `COUNT(DISTINCT DATE(STR_TO_DATE(tcf.cf_792, '{$format}')))`
**Impact**: Consultants are no longer penalized for multiple tickets on the same day.

### 2. Implemented Month-Wise Calculation
**Problem**: When date ranges span multiple months, consultants with tickets before start date were incorrectly filtered out.
**Solution**: Split calculation into two parts:
- **Start Month**: Check full month capacity first (1st to end of month)
- **Remaining Months**: Calculate from month start to end date
**Impact**: Consultants are judged on availability from their start date, not previous commitments.

### 3. Added Same-Month Date Range Support
**Problem**: Query failed when start and end dates are in the same month due to invalid date ranges.
**Solution**: Added separate logic for same-month scenarios with appropriate ticket counting.
**Impact**: Query works correctly for both same-month and cross-month date ranges.

### 4. Fixed Working Days Calculation
**Problem**: `buildWorkingDaysExpression` was using `LEAST(working_days, overlap_days)` which limited calculations incorrectly.
**Solution**: Updated to use actual overlap days without artificial limits.
**Impact**: Consultants get credit for all calendar days in their working period.

### 5. Added First Month Mandatory Free Days
**Problem**: Client requirement for mandatory minimum free days in first month based on quantity.
**Solution**: 
- Calculate month count: `$monthCount = max(1, $monthsDiff)`
- Calculate mandatory free days: `$firstMonthMandatoryFreeDays = max(1, floor($manday / $monthCount))`
- Add WHERE condition: `AND (start_month_partial_working_days - start_month_tickets) >= {$firstMonthMandatoryFreeDays}`
**Impact**: Only consultants meeting the mandatory first month free days requirement are shown.

## Technical Details

### Query Structure
The query now includes:
- `start_month_tickets`: Total tickets in start month
- `start_month_partial_free_days`: Free days from start date onwards
- `remaining_month_tickets`: Tickets in remaining months
- `total_free_days`: Combined free days for entire period

### Calculation Examples

#### Example 1: Qty 48, April to June (3 months)
- Month count: 3
- Mandatory free days: `floor(48/3) = 16`
- First month must have ≥16 free days

#### Example 2: Qty 24, April to May (2 months)  
- Month count: 2
- Mandatory free days: `floor(24/2) = 12`
- First month must have ≥12 free days

#### Example 3: Qty 10, April to April (1 year = 12 months)
- Month count: 12
- Mandatory free days: `floor(10/12) = 1`
- First month must have ≥1 free day

## Files Modified
- `modules/ServiceCompetency/models/ListView.php`
  - `getCustomQueryForPopup()` function
  - `buildWorkingDaysExpression()` function

## Benefits
1. **Accurate Availability**: Consultants shown based on actual availability for selected period
2. **Fair Assessment**: Previous commitments don't unfairly affect current availability
3. **Client Requirements**: Mandatory free days requirement enforced automatically
4. **Flexible Duration**: Works for any date range and quantity combination
5. **Performance Optimized**: Efficient SQL with proper indexing considerations

## Usage
The consultant search popup now accurately reflects consultant availability considering:
- Unique ticket days (not total tickets)
- Month-wise capacity checking
- Mandatory first month free days requirements
- Cross-month date range support
- Dynamic calculation based on project duration and quantity
