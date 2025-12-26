<?php

namespace App\Enums;

enum OrderStatus: string
{
  case PENDING = 'pending';
  case APPROVED = 'approved';
  case REJECTED = 'rejected';
  case COMPLETED = 'completed';

  /**
   * Get the display label for the status.
   */
  public function label(): string
  {
    return match ($this) {
      self::PENDING => 'Pending',
      self::APPROVED => 'Approved',
      self::REJECTED => 'Rejected',
      self::COMPLETED => 'Completed',
    };
  }

  /**
   * Get the color class for the status.
   */
  public function color(): string
  {
    return match ($this) {
      self::PENDING => 'warning',
      self::APPROVED => 'info',
      self::REJECTED => 'error',
      self::COMPLETED => 'success',
    };
  }
}
